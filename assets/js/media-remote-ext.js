(function($){
/*
 * wp.media.editor.send.attachment remote extension
 */
oldSendAttachment = wp.media.editor.send.attachment;
wp.media.editor.send.attachment = function( props, attachment ) {
	if ( 'remote' !== attachment.type ) {
		return oldSendAttachment(props, attachment);
	}
	var caption = attachment.caption,
	options, html;

	// If captions are disabled, clear the caption.
	if ( ! wp.media.view.settings.captions )
		delete attachment.caption;

	props = wp.media.string.props( props, attachment );

	options = {
		id: attachment.id,
        title: attachment.title,
		type: attachment.type,
        subtype: attachment.subtype,
        remotetype: attachment.remotetype,
		accountId: attachment.accountId || 0,
        remotedata: attachment.remotedata || [],
	};

	if ( props.linkUrl )
		options.url = props.linkUrl;

	if ( 'image' === attachment.remotetype ) {
        _.each({
            align: 'align',
            size:  'image-size',
            alt:   'image_alt',
        }, function( option, prop ) {
            if ( props[ prop ] )
                options[ option ] = props[ prop ];
        });
        options.width  = attachment.width || 0;
        options.height = attachment.height || 0;
        options.imgurl = attachment.url || options.url;
    }

	return wp.media.post( 'send-remote-attachment-to-editor', {
		nonce:      rmlSendToEditorParams.nonce,
		attachment: options,
		post_id:    wp.media.view.settings.post.id
	});
};
/**
 * wp.media.view.RemoteUploaderInline
 */
wp.media.view.RemoteUploaderInline = wp.media.View.extend({
	tagName:   'div',
	className: 'remote-uploader',
	template:  wp.media.template('remote-media-upload'),
	// bind view events
	events: {
		'input':  'refresh',
		'keyup':  'refresh',
		'change': 'refresh'
	},
	initialize: function()
	{
		_.defaults( this.options, {
			message: '',
			status:  true
		});
		
		var state = this.controller.state();
		var template = state.get('uploadTemplate');
		if (template) {
			this.template = wp.media.template(template);
		}
	},
	
	render: function()
	{
		wp.media.View.prototype.render.apply( this, arguments );
	    this.refresh();
	    return this;
	},
	
	refresh: function( event ) {},
    hide: function() {
        this.$el.addClass( 'hidden' );
    }
});
/**
 * 
 */
wp.media.remotequery = function( props ) {
	return new wp.media.model.RemoteAttachments( null, {
		props: _.extend( _.defaults( props || {}, { orderby: 'date' } ), { query: true } )
	});
};
    
wp.media.model.RemoteAttachments = wp.media.model.Attachments.extend({
	initialize: function() {
		wp.media.model.Attachments.prototype.initialize.apply( this, arguments );
    },
    _requery: function() {
		if ( this.props.get('query') )
			this.mirror( wp.media.model.RemoteQuery.get( this.props.toJSON() ) );
	}
});

wp.media.model.RemoteQuery = wp.media.model.Query.extend({
		initialize: function() {
			wp.media.model.Query.prototype.initialize.apply( this, arguments );
		},
		sync: function( method, model, options ) {
			var fallback;
	
			// Overload the read method so Attachment.fetch() functions correctly.
			if ( 'read' === method ) {
				options = options || {};
				options.context = this;
				options.data = _.extend( options.data || {}, {
					action:  'query-remote-attachments',
					post_id: wp.media.model.settings.post.id,
                    security: rmlQueryAttachmentsParams.nonce
				});
	
				// Clone the args so manipulation is non-destructive.
				args = _.clone( this.args );
	
				// Determine which page to query.
				if ( -1 !== args.posts_per_page )
					args.paged = Math.floor( this.length / args.posts_per_page ) + 1;
	
				options.data.query = args;
				return wp.media.ajax( options );
	
			// Otherwise, fall back to Backbone.sync()
			} else {
				fallback = wp.media.model.Attachments.prototype.sync ? wp.media.model.Attachments.prototype : Backbone;
				return fallback.sync.apply( this, arguments );
			}
		}
	}, {
		// Caches query objects so queries can be easily reused.
		get: (function(){
			var queries = [];

			return function( props, options ) {
				var args     = {},
					orderby  = wp.media.model.RemoteQuery.orderby,
					defaults = wp.media.model.RemoteQuery.defaultProps,
					query;

				// Remove the `query` property. This isn't linked to a query,
				// this *is* the query.
				delete props.query;

				// Fill default args.
				_.defaults( props, defaults );

				// Normalize the order.
				props.order = props.order.toUpperCase();
				if ( 'DESC' !== props.order && 'ASC' !== props.order )
					props.order = defaults.order.toUpperCase();

				// Ensure we have a valid orderby value.
				if ( ! _.contains( orderby.allowed, props.orderby ) )
					props.orderby = defaults.orderby;

				// Generate the query `args` object.
				// Correct any differing property names.
				_.each( props, function( value, prop ) {
					if ( _.isNull( value ) )
						return;

					args[ wp.media.model.RemoteQuery.propmap[ prop ] || prop ] = value;
				});

				// Fill any other default query args.
				_.defaults( args, wp.media.model.RemoteQuery.defaultArgs );

				// `props.orderby` does not always map directly to `args.orderby`.
				// Substitute exceptions specified in orderby.keymap.
				args.orderby = orderby.valuemap[ props.orderby ] || props.orderby;

				// Search the query cache for matches.
				query = _.find( queries, function( query ) {
					return _.isEqual( query.args, args );
				});

				// Otherwise, create a new query and add it to the cache.
				if ( ! query ) {
					query = new wp.media.model.RemoteQuery( [], _.extend( options || {}, {
						props: props,
						args:  args
					} ) );
					queries.push( query );
				}

				return query;
			};
		}())
});
// Extends the default MediaFrame.Post view
/**
 * 
 */
wp.media.controller.RemoteLibrary = wp.media.controller.Library.extend({
    defaults: {
        id:         'remote-library',
        multiple:   'add', // false, 'add', 'reset'
        describe:   false,
        toolbar:    'select',
        sidebar:    'settings',
        content:    'upload',
        router:     'browse',
        menu:       'default',
        remote:     true,
        searchable: true,
        filterable: false,
        sortable:   true,

        // Uses a user setting to override the content mode.
        contentUserSetting: true,

        // Sync the selection from the last state when 'multiple' matches.
        syncSelection: true
    }
});
/**
 * New wp.media.view.MediaFrame.Post
 */
var oldMediaFrame = wp.media.view.MediaFrame.Post;
wp.media.view.MediaFrame.Post = oldMediaFrame.extend({

    initialize: function() {
        oldMediaFrame.prototype.initialize.apply( this, arguments );
    },
    
    createStates: function() {
        oldMediaFrame.prototype.createStates.apply( this, arguments );

        var options = this.options;
        var that = this;
    
        //TODO get service setting template from remote service setting object
        _.each(wp.media.view.settings.remoteMediaAccounts,function(account) {
            that.states.add([
                new wp.media.controller.RemoteLibrary({
                    id:         'remote-library-'+account.id,
                    sectionid:  account.id,
                    title:      account.title,
                    service:    account.type,
                    priority:   30,
                    toolbar:    'main-remote',
                    uploadTemplate: _.isUndefined( wp.media.view.settings.remoteServiceSettings[account.type] ) ? null : wp.media.view.settings.remoteServiceSettings[account.type].uploadTemplate,
                    filterable: 'uploaded',
                    library:  wp.media.remotequery( _.defaults({
                        type: 'remote',
                        account_id: account.id
                    }, options.library ) ),
                    state:    'remote-library-'+account.id,
                    editable:   true,
                    displaySettings: true,
                    displayUserSettings: true,
                    //content:    'remote-upload',
                    menu:       'default',
                    AttachmentView: wp.media.view.Attachment.RemoteLibrary
                })
            ]);
        }, this);
    },
    bindHandlers: function() {
        oldMediaFrame.prototype.bindHandlers.apply( this, arguments );

        this.on( 'toolbar:create:main-remote', this.createToolbar, this );
        this.on( 'toolbar:render:main-remote', this.mainInsertToolbar, this );
    },
    
    uploadContent: function() {
        var sectionid = this.state().get('sectionid');
        if (sectionid) {
            this.$el.removeClass('hide-toolbar');
            this.content.set(new wp.media.view.RemoteUploaderInline({
                controller: this,
                model: this.state().props
            }));
        } else {
            wp.media.view.MediaFrame.Select.prototype.uploadContent.apply( this, arguments );
        }
    }
});

wp.media.view.RemoteAttachmentsBrowser = wp.media.view.AttachmentsBrowser.extend({
    createUploader: function() {
        this.uploader = new wp.media.view.RemoteUploaderInline({
            controller: this.controller,
            status:     false,
            message:    this.controller.isModeActive( 'grid' ) ? '' : wp.media.view.l10n.noItemsFound,
            canClose:   this.controller.isModeActive( 'grid' )
        });

        this.uploader.hide();
        this.views.add( this.uploader );
    },
    createSingle: function () {
        var sidebar = this.sidebar,
            single = this.options.selection.single(),
            type = single.get('type'),
            remotetype = single.get('remotetype');

        if (type !== 'remote') {
            return wp.media.view.AttachmentsBrowser.prototype.createSingle.apply( this, arguments );
        }
        //Set type from remote type to display same attachment display settings than native supported type

        if (remotetype === 'image') {
            single.set('type', remotetype);
            wp.media.view.AttachmentsBrowser.prototype.createSingle.apply( this, arguments );
            single.set('type', 'remote');
        }
        
        wp.media.view.AttachmentsBrowser.prototype.createSingle.apply( this, arguments );
        
        // Show the sidebar on mobile
        if ( this.model.id === 'remote-library-'+this.model.get('sectionid') ) {
            sidebar.$el.addClass( 'visible' );
        }
    }
});
var oldMediaFrameSelect = wp.media.view.MediaFrame.Select;
wp.media.view.MediaFrame.Select.prototype.browseRemoteContent = function( contentRegion ) {
        var state = this.state(),
            isRemoteLibrary = state.get('remote');
        if (isRemoteLibrary === true) {
            this.$el.removeClass('hide-toolbar');

            // Browse our library of attachments.
            contentRegion.view = new wp.media.view.RemoteAttachmentsBrowser({
                controller: this,
                collection: state.get('library'),
                selection:  state.get('selection'),
                model:      state,
                sortable:   state.get('sortable'),
                search:     state.get('searchable'),
                filters:    state.get('filterable'),
                display:    state.has('display') ? state.get('display') : state.get('displaySettings'),
                dragInfo:   state.get('dragInfo'),

                idealColumnWidth: state.get('idealColumnWidth'),
                suggestedWidth:   state.get('suggestedWidth'),
                suggestedHeight:  state.get('suggestedHeight'),

                AttachmentView: state.get('AttachmentView')
            });
        } else {
            oldMediaFrameSelect.prototype.browseContent.apply( this, arguments );
        }
    }
wp.media.view.MediaFrame.Select = oldMediaFrameSelect.extend({
    /**
     * Bind region mode event callbacks.
     *
     * @see media.controller.Region.render
     */
    bindHandlers: function() {
        this.on( 'router:create:browse', this.createRouter, this );
        this.on( 'router:render:browse', this.browseRouter, this );
        this.on( 'content:create:browse', this.browseRemoteContent, this );
        this.on( 'content:render:upload', this.uploadContent, this );
        this.on( 'toolbar:create:select', this.createSelectToolbar, this );
    },
});
/**
 * wp.media.view.Attachment.RemoteLibrary
 */
wp.media.view.Attachment.RemoteLibrary = wp.media.view.Attachment.Library.extend({
    template:  wp.media.template('attachment-remote'),
    toggleSelection: function( ) {
        wp.media.view.Attachment.Library.prototype.toggleSelection.apply( this, arguments );
    }
});
/**
 * wp.media.view.Attachment.Selection
 */
wp.media.view.Attachment.RemoteSelection = wp.media.view.Attachment.Selection.extend({
    template:  wp.media.template('attachment-remote')
});
/**
 * wp.media.view.Attachments.Selection
 * 
 * Use new RemoteSelection view by default
 */
oldAttachmentsSelection = wp.media.view.Attachments.Selection;
wp.media.view.Attachments.Selection = oldAttachmentsSelection.extend({
    initialize: function() {
        _.defaults( this.options, {
            // The single `Attachment` view to be used in the `Attachments` view.
            AttachmentView: wp.media.view.Attachment.RemoteSelection
        });
        return oldAttachmentsSelection.prototype.initialize.apply( this, arguments );
    }
});
}(jQuery));