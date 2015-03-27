<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

use WPRemoteMediaExt\Guzzle\Http\Exception\ClientErrorResponseException;
use WPRemoteMediaExt\RemoteMediaExt\Exception;

abstract class AbstractRemoteAccount
{
    const STATUS_UNKNOWN    = 0;
    const STATUS_AUTHNEEDED = 1;
    const STATUS_INVALID    = 2;
    const STATUS_ENABLED    = 10;

    protected $localID;
    protected $remoteID;
    protected $type;

    protected $attributes;

    protected $service;

    public static function getType($aid)
    {
        $type = get_post_meta($aid, 'remote_account_type', true);

        if (empty($type)) {
            return null;
        }
        return $type;
    }

    public function __construct($aid = null, $type = null)
    {
        $this->localID = (int)$aid;
        $this->type    = $type;
        $this->fetch();
    }

    public function setId($aid)
    {
        $this->localID = $aid;

        return $this;
    }
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getId()
    {
        return $this->localID;
    }

    public function hasService(AbstractRemoteService $service)
    {
        return is_a($this->service, get_class($service));
    }

    public function setServiceFromClass($serviceClass)
    {
        $serviceInstance = RemoteServiceFactory::create($serviceClass);
        if (!is_null($serviceInstance) &&
            $serviceInstance instanceof AbstractRemoteService
        ) {
            $this->setService($serviceInstance);
        }
    }

    public function fetch()
    {
        if (!is_null($this->localID)) {
            $this->attributes = get_post_meta($this->localID, 'remote_attr', true);

            $this->type = self::getType($this->localID);

            $serviceClass = $this->get('service_class');

            if (!empty($serviceClass)) {
                $serviceClass = RemoteServiceFactory::retrieveClassName($serviceClass);
                $this->setServiceFromClass($serviceClass);
            }
        }

        return $this;
    }

    /*
     * @return false on failure or no change update true on update
     *
     */
    public function save()
    {
        $this->set('service_class', RemoteServiceFactory::getDbClassName(RemoteServiceFactory::getClass($this->type)));
        $return = update_post_meta($this->localID, 'remote_attr', $this->attributes);
        $return = update_post_meta($this->localID, 'remote_account_type', $this->type) && $return;

        return $return;
    }

    public function setService(AbstractRemoteService $service)
    {
        $this->service = $service;
        if (!is_null($this->service)) {
            $this->service->setAccount($this);
        }
        return $this;
    }

    public function getServiceNamespace()
    {
        $serviceclass = get_class($this->service);
        return substr($serviceclass, 0, strrpos($serviceclass, '\\'));

    }
    
    public function getService()
    {
        return $this->service;
    }

    public function get($name, $default = null)
    {
        if ($name === null) {
            return $this->attributes;
        }

        if ($name === 'type') {
            return $this->type;
        }

        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    public function destroy($name)
    {
        unset($this->attributes[$name]);

        return $this;
    }

    public function set($name, $value)
    {
        if ($name == 'remote_account_type') {
            $this->type = $value;
            return $this;
        }
        $this->attributes[$name] = $value;

        return $this;
    }

    public function isValid()
    {
        //Note:
        //Run in a hook at least after plugin_loaded
        //If advanced extensions modules are not done loading it will return false
        $serviceClass = $this->get('service_class');
        $serviceClass = RemoteServiceFactory::retrieveClassName($serviceClass);

        if (!class_exists($serviceClass)) {
            return false;
        }

        $isValid = $this->get('isValid');
        if (isset($isValid) &&
            $isValid === true
        ) {
            return true;
        }

        return false;
    }

    public function isEnabled()
    {
        $isEnabled = $this->get('isEnabled', false);
        if ($isEnabled === true) {
            return true;
        }

        $isValid = $this->get('isValid', false);
        $isAuthNeeded = $this->isAuthNeeded();
        if ($isAuthNeeded === false &&
            $isValid === true
        ) {
            return true;
        }
        return false;
    }

    public function isAuthNeeded()
    {
        if ($this->service instanceof AbstractAuthService) {
            return true;
        }

        return false;
    }

    public function getAuthUrl()
    {
        if (!$this->isAuthNeeded() ||
            !$this->service instanceof AbstractAuthService
        ) {
            return '';
        }

        return $this->service->getAuthUrl();
    }

    public function getStatusDisplay()
    {
        $status = $this->getStatus();
        switch ($status) {
            case self::STATUS_ENABLED:
                return __('Enabled', 'remote-medias-lite');
            case self::STATUS_AUTHNEEDED:
                return __('Authenticate now', 'remote-medias-lite');
            case self::STATUS_INVALID:
                return __('Invalid', 'remote-medias-lite');
            default:
                return __('Unknown', 'remote-medias-lite');
        }

        return __('Unknown', 'remote-medias-lite');
    }

    public function getStatus()
    {
        if ($this->isEnabled()) {
            return self::STATUS_ENABLED;
        }

        if ($this->isAuthNeeded()) {
            return self::STATUS_AUTHNEEDED;
        }

        $isValid = $this->get('isValid');
        if ($isValid === false) {
            return self::STATUS_INVALID;
        }
        return self::STATUS_UNKNOWN;
    }

    /*
    * @return 1 on validate, 0 on invalid params, -1 on error
    */
    public function validate()
    {
        $isValid = 0;
        $this->set('isValid', false);

        try {
            $isServiceValid = $this->service->validate();
            if ($isServiceValid === true) {
                $this->set('isValid', true);
                $isValid = 1;
            }
        } catch (ClientErrorResponseException $e) {
            $isValid = 0;
        } catch (Exception\InvalidAuthParamException $e) {
            $isValid = 0;
        } catch (\Exception $e) {
            error_log('RML error occured validating account '.$this->getId().': '.$e->getMessage());
            $isValid = -1;
        }

        return $isValid;
    }
}
