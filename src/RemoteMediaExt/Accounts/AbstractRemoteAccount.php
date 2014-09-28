<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

use WPRemoteMediaExt\Guzzle\Http\Exception\ClientErrorResponseException;

abstract class AbstractRemoteAccount
{
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

    public function getId()
    {
        return $this->localID;
    }

    public function hasService(AbstractRemoteService $service)
    {
        return is_a($this->service, get_class($service));
    }

    public function fetch()
    {
        if (!is_null($this->localID)) {
            $this->attributes = get_post_meta($this->localID, 'remote_attr', true);

            $this->type = self::getType($this->localID);

            $serviceClass = $this->get('service_class');

            if (!empty($serviceClass)) {
                $serviceClass = RemoteServiceFactory::retrieveClassName($serviceClass);

                $this->setService(RemoteServiceFactory::create($serviceClass));
            }
        }

        return $this;
    }

    /*
     * return false on failure true if success
     */
    public function save()
    {
        $this->validate();

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

    public function getService()
    {
        return $this->service;
    }

    public function get($name)
    {
        if ($name === null) {
            return $this->attributes;
        }

        if ($name === 'type') {
            return $this->type;
        }

        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
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
            $this->set('service_class', RemoteServiceFactory::getDbClassName(RemoteServiceFactory::getClass($this->type)));
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

    public function validate()
    {
        if (!($this->service instanceof AbstractRemoteService)) {
            return false;
        }

        $return = array();
        $return['validate'] = false;
        
        try {
            $return['validate'] = $this->service->validate($this);
        } catch (ClientErrorResponseException $e) {
            $return['error'] = true;
            $return['statuscode'] = $e->getResponse()->getStatusCode();
            $return['msg']        = $e->getResponse()->getReasonPhrase();
        } catch (\Exception $e) {
            $return['error'] = true;
            $return['statuscode'] = $e->getCode();
            $return['msg']        = $e->getMessage();
        }

        $lastValidQuery = null;

        if ($return['validate'] === true) {
            $lastValidQuery =  $this->get('remote_user_id');
            $this->set('isValid', true);
        } else {
            $this->set('isValid', false);
        }

        $this->set('last_valid_query', $lastValidQuery);

        return $return['validate'];
    }
}
