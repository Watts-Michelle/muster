<?php

$csversion = getCometServiceVersion();

if($csversion==2){
    $devmodetext='';
    if(defined('DEV_MODE') && DEV_MODE=='1'){
        $devmodetext = "?dev=1";
    }
    $csscripturl = CS2_TEXTCHAT_SERVER.'/getjs/'.$devmodetext;
    ?>
    jqcc.ajax({
        url: location.protocol+'//<?php echo $csscripturl; ?>',
        dataType: "script",
        success: initializeCometService,
        cache: true
    });
<?php
}elseif($csversion==1 && USE_CS_LEGACY=='0'){
?>
/*! 4.8.0 / Consumer  */
(function webpackUniversalModuleDefinition(root, factory) {
        root["CometService"] = factory();
})(this, function() {
return /******/ (function(modules) { // webpackBootstrap
/******/    // The module cache
/******/    var installedModules = {};

/******/    // The require function
/******/    function __webpack_require__(moduleId) {

/******/        // Check if module is in cache
/******/        if(installedModules[moduleId])
/******/            return installedModules[moduleId].exports;

/******/        // Create a new module (and put it into the cache)
/******/        var module = installedModules[moduleId] = {
/******/            exports: {},
/******/            id: moduleId,
/******/            loaded: false
/******/        };

/******/        // Execute the module function
/******/        modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/        // Flag the module as loaded
/******/        module.loaded = true;

/******/        // Return the exports of the module
/******/        return module.exports;
/******/    }


/******/    // expose the modules object (__webpack_modules__)
/******/    __webpack_require__.m = modules;

/******/    // expose the module cache
/******/    __webpack_require__.c = installedModules;

/******/    // __webpack_public_path__
/******/    __webpack_require__.p = "";

/******/    // Load entry module and return exports
/******/    return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });

    var _pubnubCommon = __webpack_require__(1);

    var _pubnubCommon2 = _interopRequireDefault(_pubnubCommon);

    var _networking = __webpack_require__(40);

    var _networking2 = _interopRequireDefault(_networking);

    var _web = __webpack_require__(41);

    var _web2 = _interopRequireDefault(_web);

    var _webNode = __webpack_require__(42);

    var _flow_interfaces = __webpack_require__(8);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

    function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

    function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

    function sendBeacon(url) {
      if (navigator && navigator.sendBeacon) {
        navigator.sendBeacon(url);
      } else {
        return false;
      }
    }

    var _class = function (_PubNubCore) {
      _inherits(_class, _PubNubCore);

      function _class(setup) {
        _classCallCheck(this, _class);

        var _setup$listenToBrowse = setup.listenToBrowserNetworkEvents,
            listenToBrowserNetworkEvents = _setup$listenToBrowse === undefined ? true : _setup$listenToBrowse;


        setup.db = _web2.default;
        setup.sdkFamily = 'Web';
        setup.networking = new _networking2.default({ get: _webNode.get, post: _webNode.post, sendBeacon: sendBeacon });

        var _this = _possibleConstructorReturn(this, (_class.__proto__ || Object.getPrototypeOf(_class)).call(this, setup));

        if (listenToBrowserNetworkEvents) {
          window.addEventListener('offline', function () {
            _this.networkDownDetected();
          });

          window.addEventListener('online', function () {
            _this.networkUpDetected();
          });
        }
        return _this;
      }

      return _class;
    }(_pubnubCommon2.default);

    exports.default = _class;
    module.exports = exports['default'];

/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });

    var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

    var _uuid = __webpack_require__(2);

    var _uuid2 = _interopRequireDefault(_uuid);

    var _config = __webpack_require__(7);

    var _config2 = _interopRequireDefault(_config);

    var _index = __webpack_require__(9);

    var _index2 = _interopRequireDefault(_index);

    var _subscription_manager = __webpack_require__(11);

    var _subscription_manager2 = _interopRequireDefault(_subscription_manager);

    var _listener_manager = __webpack_require__(12);

    var _listener_manager2 = _interopRequireDefault(_listener_manager);

    var _endpoint = __webpack_require__(18);

    var _endpoint2 = _interopRequireDefault(_endpoint);

    var _add_channels = __webpack_require__(19);

    var addChannelsChannelGroupConfig = _interopRequireWildcard(_add_channels);

    var _remove_channels = __webpack_require__(20);

    var removeChannelsChannelGroupConfig = _interopRequireWildcard(_remove_channels);

    var _delete_group = __webpack_require__(21);

    var deleteChannelGroupConfig = _interopRequireWildcard(_delete_group);

    var _list_groups = __webpack_require__(22);

    var listChannelGroupsConfig = _interopRequireWildcard(_list_groups);

    var _list_channels = __webpack_require__(23);

    var listChannelsInChannelGroupConfig = _interopRequireWildcard(_list_channels);

    var _add_push_channels = __webpack_require__(24);

    var addPushChannelsConfig = _interopRequireWildcard(_add_push_channels);

    var _remove_push_channels = __webpack_require__(25);

    var removePushChannelsConfig = _interopRequireWildcard(_remove_push_channels);

    var _list_push_channels = __webpack_require__(26);

    var listPushChannelsConfig = _interopRequireWildcard(_list_push_channels);

    var _remove_device = __webpack_require__(27);

    var removeDevicePushConfig = _interopRequireWildcard(_remove_device);

    var _leave = __webpack_require__(28);

    var presenceLeaveEndpointConfig = _interopRequireWildcard(_leave);

    var _where_now = __webpack_require__(29);

    var presenceWhereNowEndpointConfig = _interopRequireWildcard(_where_now);

    var _heartbeat = __webpack_require__(30);

    var presenceHeartbeatEndpointConfig = _interopRequireWildcard(_heartbeat);

    var _get_state = __webpack_require__(31);

    var presenceGetStateConfig = _interopRequireWildcard(_get_state);

    var _set_state = __webpack_require__(32);

    var presenceSetStateConfig = _interopRequireWildcard(_set_state);

    var _here_now = __webpack_require__(33);

    var presenceHereNowConfig = _interopRequireWildcard(_here_now);

    var _audit = __webpack_require__(34);

    var auditEndpointConfig = _interopRequireWildcard(_audit);

    var _grant = __webpack_require__(35);

    var grantEndpointConfig = _interopRequireWildcard(_grant);

    var _publish = __webpack_require__(36);

    var publishEndpointConfig = _interopRequireWildcard(_publish);

    var _history = __webpack_require__(37);

    var historyEndpointConfig = _interopRequireWildcard(_history);

    var _fetch_messages = __webpack_require__(38);

    var fetchMessagesEndpointConfig = _interopRequireWildcard(_fetch_messages);

    var _time = __webpack_require__(15);

    var timeEndpointConfig = _interopRequireWildcard(_time);

    var _subscribe = __webpack_require__(39);

    var subscribeEndpointConfig = _interopRequireWildcard(_subscribe);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _categories = __webpack_require__(13);

    var _categories2 = _interopRequireDefault(_categories);

    var _flow_interfaces = __webpack_require__(8);

    function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } else { var newObj = {}; if (obj != null) { for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) newObj[key] = obj[key]; } } newObj.default = obj; return newObj; } }

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

    var _class = function () {
      function _class(setup) {
        var _this = this;

        _classCallCheck(this, _class);

        var db = setup.db,
            networking = setup.networking;


        var config = this._config = new _config2.default({ setup: setup, db: db });
        var crypto = new _index2.default({ config: config });

        networking.init(config);

        var modules = { config: config, networking: networking, crypto: crypto };

        var timeEndpoint = _endpoint2.default.bind(this, modules, timeEndpointConfig);
        var leaveEndpoint = _endpoint2.default.bind(this, modules, presenceLeaveEndpointConfig);
        var heartbeatEndpoint = _endpoint2.default.bind(this, modules, presenceHeartbeatEndpointConfig);
        var setStateEndpoint = _endpoint2.default.bind(this, modules, presenceSetStateConfig);
        var subscribeEndpoint = _endpoint2.default.bind(this, modules, subscribeEndpointConfig);

        var listenerManager = this._listenerManager = new _listener_manager2.default();

        var subscriptionManager = new _subscription_manager2.default({
          timeEndpoint: timeEndpoint,
          leaveEndpoint: leaveEndpoint,
          heartbeatEndpoint: heartbeatEndpoint,
          setStateEndpoint: setStateEndpoint,
          subscribeEndpoint: subscribeEndpoint,
          crypto: modules.crypto,
          config: modules.config,
          listenerManager: listenerManager
        });

        this.addListener = listenerManager.addListener.bind(listenerManager);
        this.removeListener = listenerManager.removeListener.bind(listenerManager);
        this.removeAllListeners = listenerManager.removeAllListeners.bind(listenerManager);

        this.channelGroups = {
          listGroups: _endpoint2.default.bind(this, modules, listChannelGroupsConfig),
          listChannels: _endpoint2.default.bind(this, modules, listChannelsInChannelGroupConfig),
          addChannels: _endpoint2.default.bind(this, modules, addChannelsChannelGroupConfig),
          removeChannels: _endpoint2.default.bind(this, modules, removeChannelsChannelGroupConfig),
          deleteGroup: _endpoint2.default.bind(this, modules, deleteChannelGroupConfig)
        };

        this.push = {
          addChannels: _endpoint2.default.bind(this, modules, addPushChannelsConfig),
          removeChannels: _endpoint2.default.bind(this, modules, removePushChannelsConfig),
          deleteDevice: _endpoint2.default.bind(this, modules, removeDevicePushConfig),
          listChannels: _endpoint2.default.bind(this, modules, listPushChannelsConfig)
        };

        this.hereNow = _endpoint2.default.bind(this, modules, presenceHereNowConfig);
        this.whereNow = _endpoint2.default.bind(this, modules, presenceWhereNowEndpointConfig);
        this.getState = _endpoint2.default.bind(this, modules, presenceGetStateConfig);
        this.setState = subscriptionManager.adaptStateChange.bind(subscriptionManager);

        this.grant = _endpoint2.default.bind(this, modules, grantEndpointConfig);
        this.audit = _endpoint2.default.bind(this, modules, auditEndpointConfig);

        this.publish = _endpoint2.default.bind(this, modules, publishEndpointConfig);

        this.fire = function (args, callback) {
          args.replicate = false;
          args.storeInHistory = false;
          _this.publish(args, callback);
        };

        this.history = _endpoint2.default.bind(this, modules, historyEndpointConfig);
        this.fetchMessages = _endpoint2.default.bind(this, modules, fetchMessagesEndpointConfig);

        this.time = timeEndpoint;

        this.subscribe = subscriptionManager.adaptSubscribeChange.bind(subscriptionManager);
        this.unsubscribe = subscriptionManager.adaptUnsubscribeChange.bind(subscriptionManager);
        this.disconnect = subscriptionManager.disconnect.bind(subscriptionManager);
        this.reconnect = subscriptionManager.reconnect.bind(subscriptionManager);

        this.destroy = function (isOffline) {
          subscriptionManager.unsubscribeAll(isOffline);
          subscriptionManager.disconnect();
        };

        this.stop = this.destroy;

        this.unsubscribeAll = subscriptionManager.unsubscribeAll.bind(subscriptionManager);

        this.getSubscribedChannels = subscriptionManager.getSubscribedChannels.bind(subscriptionManager);
        this.getSubscribedChannelGroups = subscriptionManager.getSubscribedChannelGroups.bind(subscriptionManager);

        this.encrypt = crypto.encrypt.bind(crypto);
        this.decrypt = crypto.decrypt.bind(crypto);

        this.getAuthKey = modules.config.getAuthKey.bind(modules.config);
        this.setAuthKey = modules.config.setAuthKey.bind(modules.config);
        this.setCipherKey = modules.config.setCipherKey.bind(modules.config);
        this.getUUID = modules.config.getUUID.bind(modules.config);
        this.setUUID = modules.config.setUUID.bind(modules.config);
        this.getFilterExpression = modules.config.getFilterExpression.bind(modules.config);
        this.setFilterExpression = modules.config.setFilterExpression.bind(modules.config);
      }

      _createClass(_class, [{
        key: 'getVersion',
        value: function getVersion() {
          return this._config.getVersion();
        }
      }, {
        key: 'networkDownDetected',
        value: function networkDownDetected() {
          this._listenerManager.announceNetworkDown();

          if (this._config.restore) {
            this.disconnect();
          } else {
            this.destroy(true);
          }
        }
      }, {
        key: 'networkUpDetected',
        value: function networkUpDetected() {
          this._listenerManager.announceNetworkUp();
          this.reconnect();
        }
      }], [{
        key: 'generateUUID',
        value: function generateUUID() {
          return _uuid2.default.v4();
        }
      }]);

      return _class;
    }();

    _class.OPERATIONS = _operations2.default;
    _class.CATEGORIES = _categories2.default;
    exports.default = _class;
    module.exports = exports['default'];

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

    var v1 = __webpack_require__(3);
    var v4 = __webpack_require__(6);

    var uuid = v4;
    uuid.v1 = v1;
    uuid.v4 = v4;

    module.exports = uuid;


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

    // Unique ID creation requires a high quality random # generator.  We feature
    // detect to determine the best RNG source, normalizing to a function that
    // returns 128-bits of randomness, since that's what's usually required
    var rng = __webpack_require__(4);
    var bytesToUuid = __webpack_require__(5);

    // **`v1()` - Generate time-based UUID**
    //
    // Inspired by https://github.com/LiosK/UUID.js
    // and http://docs.python.org/library/uuid.html

    // random #'s we need to init node and clockseq
    var _seedBytes = rng();

    // Per 4.5, create and 48-bit node id, (47 random bits + multicast bit = 1)
    var _nodeId = [
      _seedBytes[0] | 0x01,
      _seedBytes[1], _seedBytes[2], _seedBytes[3], _seedBytes[4], _seedBytes[5]
    ];

    // Per 4.2.2, randomize (14 bit) clockseq
    var _clockseq = (_seedBytes[6] << 8 | _seedBytes[7]) & 0x3fff;

    // Previous uuid creation time
    var _lastMSecs = 0, _lastNSecs = 0;

    // See https://github.com/broofa/node-uuid for API details
    function v1(options, buf, offset) {
      var i = buf && offset || 0;
      var b = buf || [];

      options = options || {};

      var clockseq = options.clockseq !== undefined ? options.clockseq : _clockseq;

      // UUID timestamps are 100 nano-second units since the Gregorian epoch,
      // (1582-10-15 00:00).  JSNumbers aren't precise enough for this, so
      // time is handled internally as 'msecs' (integer milliseconds) and 'nsecs'
      // (100-nanoseconds offset from msecs) since unix epoch, 1970-01-01 00:00.
      var msecs = options.msecs !== undefined ? options.msecs : new Date().getTime();

      // Per 4.2.1.2, use count of uuid's generated during the current clock
      // cycle to simulate higher resolution clock
      var nsecs = options.nsecs !== undefined ? options.nsecs : _lastNSecs + 1;

      // Time since last uuid creation (in msecs)
      var dt = (msecs - _lastMSecs) + (nsecs - _lastNSecs)/10000;

      // Per 4.2.1.2, Bump clockseq on clock regression
      if (dt < 0 && options.clockseq === undefined) {
        clockseq = clockseq + 1 & 0x3fff;
      }

      // Reset nsecs if clock regresses (new clockseq) or we've moved onto a new
      // time interval
      if ((dt < 0 || msecs > _lastMSecs) && options.nsecs === undefined) {
        nsecs = 0;
      }

      // Per 4.2.1.2 Throw error if too many uuids are requested
      if (nsecs >= 10000) {
        throw new Error('uuid.v1(): Can\'t create more than 10M uuids/sec');
      }

      _lastMSecs = msecs;
      _lastNSecs = nsecs;
      _clockseq = clockseq;

      // Per 4.1.4 - Convert from unix epoch to Gregorian epoch
      msecs += 12219292800000;

      // `time_low`
      var tl = ((msecs & 0xfffffff) * 10000 + nsecs) % 0x100000000;
      b[i++] = tl >>> 24 & 0xff;
      b[i++] = tl >>> 16 & 0xff;
      b[i++] = tl >>> 8 & 0xff;
      b[i++] = tl & 0xff;

      // `time_mid`
      var tmh = (msecs / 0x100000000 * 10000) & 0xfffffff;
      b[i++] = tmh >>> 8 & 0xff;
      b[i++] = tmh & 0xff;

      // `time_high_and_version`
      b[i++] = tmh >>> 24 & 0xf | 0x10; // include version
      b[i++] = tmh >>> 16 & 0xff;

      // `clock_seq_hi_and_reserved` (Per 4.2.2 - include variant)
      b[i++] = clockseq >>> 8 | 0x80;

      // `clock_seq_low`
      b[i++] = clockseq & 0xff;

      // `node`
      var node = options.node || _nodeId;
      for (var n = 0; n < 6; ++n) {
        b[i + n] = node[n];
      }

      return buf ? buf : bytesToUuid(b);
    }

    module.exports = v1;


/***/ },
/* 4 */
/***/ function(module, exports) {

    /* WEBPACK VAR INJECTION */(function(global) {// Unique ID creation requires a high quality random # generator.  In the
    // browser this is a little complicated due to unknown quality of Math.random()
    // and inconsistent support for the `crypto` API.  We do the best we can via
    // feature-detection
    var rng;

    var crypto = global.crypto || global.msCrypto; // for IE 11
    if (crypto && crypto.getRandomValues) {
      // WHATWG crypto RNG - http://wiki.whatwg.org/wiki/Crypto
      var rnds8 = new Uint8Array(16);
      rng = function whatwgRNG() {
        crypto.getRandomValues(rnds8);
        return rnds8;
      };
    }

    if (!rng) {
      // Math.random()-based (RNG)
      //
      // If all else fails, use Math.random().  It's fast, but is of unspecified
      // quality.
      var  rnds = new Array(16);
      rng = function() {
        for (var i = 0, r; i < 16; i++) {
          if ((i & 0x03) === 0) r = Math.random() * 0x100000000;
          rnds[i] = r >>> ((i & 0x03) << 3) & 0xff;
        }

        return rnds;
      };
    }

    module.exports = rng;

    /* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 5 */
/***/ function(module, exports) {

    /**
     * Convert array of 16 byte values to UUID string format of the form:
     * XXXXXXXX-XXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
     */
    var byteToHex = [];
    for (var i = 0; i < 256; ++i) {
      byteToHex[i] = (i + 0x100).toString(16).substr(1);
    }

    function bytesToUuid(buf, offset) {
      var i = offset || 0;
      var bth = byteToHex;
      return  bth[buf[i++]] + bth[buf[i++]] +
              bth[buf[i++]] + bth[buf[i++]] + '-' +
              bth[buf[i++]] + bth[buf[i++]] + '-' +
              bth[buf[i++]] + bth[buf[i++]] + '-' +
              bth[buf[i++]] + bth[buf[i++]] + '-' +
              bth[buf[i++]] + bth[buf[i++]] +
              bth[buf[i++]] + bth[buf[i++]] +
              bth[buf[i++]] + bth[buf[i++]];
    }

    module.exports = bytesToUuid;


/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

    var rng = __webpack_require__(4);
    var bytesToUuid = __webpack_require__(5);

    function v4(options, buf, offset) {
      var i = buf && offset || 0;

      if (typeof(options) == 'string') {
        buf = options == 'binary' ? new Array(16) : null;
        options = null;
      }
      options = options || {};

      var rnds = options.random || (options.rng || rng)();

      // Per 4.4, set bits for version and `clock_seq_hi_and_reserved`
      rnds[6] = (rnds[6] & 0x0f) | 0x40;
      rnds[8] = (rnds[8] & 0x3f) | 0x80;

      // Copy bytes to buffer, if provided
      if (buf) {
        for (var ii = 0; ii < 16; ++ii) {
          buf[i + ii] = rnds[ii];
        }
      }

      return buf || bytesToUuid(rnds);
    }

    module.exports = v4;


/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });

    var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

    var _uuid = __webpack_require__(2);

    var _uuid2 = _interopRequireDefault(_uuid);

    var _flow_interfaces = __webpack_require__(8);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

    var _class = function () {
      function _class(_ref) {
        var setup = _ref.setup,
            db = _ref.db;

        _classCallCheck(this, _class);

        this._db = db;

        this.instanceId = 'pn-' + _uuid2.default.v4();
        this.secretKey = setup.secretKey || setup.secret_key;
        this.subscribeKey = setup.subscribeKey || setup.subscribe_key;
        this.publishKey = setup.publishKey || setup.publish_key;
        this.sdkFamily = setup.sdkFamily;
        this.partnerId = setup.partnerId;
        this.setAuthKey(setup.authKey);
        this.setCipherKey(setup.cipherKey);

        this.setFilterExpression(setup.filterExpression);

        this.origin = setup.origin || 'pubsub.pubnub.com';
        this.secure = setup.ssl || false;
        this.restore = setup.restore || false;
        this.proxy = setup.proxy;
        this.keepAlive = setup.keepAlive;
        this.keepAliveSettings = setup.keepAliveSettings;

        if (typeof location !== 'undefined' && location.protocol === 'https:') {
          this.secure = true;
        }

        this.logVerbosity = setup.logVerbosity || false;
        this.suppressLeaveEvents = setup.suppressLeaveEvents || false;

        this.announceFailedHeartbeats = setup.announceFailedHeartbeats || true;
        this.announceSuccessfulHeartbeats = setup.announceSuccessfulHeartbeats || false;

        this.useInstanceId = setup.useInstanceId || false;
        this.useRequestId = setup.useRequestId || false;

        this.requestMessageCountThreshold = setup.requestMessageCountThreshold;

        this.setTransactionTimeout(setup.transactionalRequestTimeout || 15 * 1000);

        this.setSubscribeTimeout(setup.subscribeRequestTimeout || 310 * 1000);

        this.setSendBeaconConfig(setup.useSendBeacon || true);

        this.setPresenceTimeout(setup.presenceTimeout || 300);

        if (setup.heartbeatInterval) {
          this.setHeartbeatInterval(setup.heartbeatInterval);
        }

        this.setUUID(this._decideUUID(setup.uuid));
      }

      _createClass(_class, [{
        key: 'getAuthKey',
        value: function getAuthKey() {
          return this.authKey;
        }
      }, {
        key: 'setAuthKey',
        value: function setAuthKey(val) {
          this.authKey = val;return this;
        }
      }, {
        key: 'setCipherKey',
        value: function setCipherKey(val) {
          this.cipherKey = val;return this;
        }
      }, {
        key: 'getUUID',
        value: function getUUID() {
          return this.UUID;
        }
      }, {
        key: 'setUUID',
        value: function setUUID(val) {
          if (this._db && this._db.set) this._db.set(this.subscribeKey + 'uuid', val);
          this.UUID = val;
          return this;
        }
      }, {
        key: 'getFilterExpression',
        value: function getFilterExpression() {
          return this.filterExpression;
        }
      }, {
        key: 'setFilterExpression',
        value: function setFilterExpression(val) {
          this.filterExpression = val;return this;
        }
      }, {
        key: 'getPresenceTimeout',
        value: function getPresenceTimeout() {
          return this._presenceTimeout;
        }
      }, {
        key: 'setPresenceTimeout',
        value: function setPresenceTimeout(val) {
          this._presenceTimeout = val;
          this.setHeartbeatInterval(this._presenceTimeout / 2 - 1);
          return this;
        }
      }, {
        key: 'getHeartbeatInterval',
        value: function getHeartbeatInterval() {
          return this._heartbeatInterval;
        }
      }, {
        key: 'setHeartbeatInterval',
        value: function setHeartbeatInterval(val) {
          this._heartbeatInterval = val;return this;
        }
      }, {
        key: 'getSubscribeTimeout',
        value: function getSubscribeTimeout() {
          return this._subscribeRequestTimeout;
        }
      }, {
        key: 'setSubscribeTimeout',
        value: function setSubscribeTimeout(val) {
          this._subscribeRequestTimeout = val;return this;
        }
      }, {
        key: 'getTransactionTimeout',
        value: function getTransactionTimeout() {
          return this._transactionalRequestTimeout;
        }
      }, {
        key: 'setTransactionTimeout',
        value: function setTransactionTimeout(val) {
          this._transactionalRequestTimeout = val;return this;
        }
      }, {
        key: 'isSendBeaconEnabled',
        value: function isSendBeaconEnabled() {
          return this._useSendBeacon;
        }
      }, {
        key: 'setSendBeaconConfig',
        value: function setSendBeaconConfig(val) {
          this._useSendBeacon = val;return this;
        }
      }, {
        key: 'getVersion',
        value: function getVersion() {
          return '4.8.0';
        }
      }, {
        key: '_decideUUID',
        value: function _decideUUID(providedUUID) {
          if (providedUUID) {
            return providedUUID;
          }

          if (this._db && this._db.get && this._db.get(this.subscribeKey + 'uuid')) {
            return this._db.get(this.subscribeKey + 'uuid');
          }

          return 'pn-' + _uuid2.default.v4();
        }
      }]);

      return _class;
    }();

    exports.default = _class;
    module.exports = exports['default'];

/***/ },
/* 8 */
/***/ function(module, exports) {

    'use strict';

    module.exports = {};

/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });

    var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

    var _config = __webpack_require__(7);

    var _config2 = _interopRequireDefault(_config);

    var _hmacSha = __webpack_require__(10);

    var _hmacSha2 = _interopRequireDefault(_hmacSha);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

    var _class = function () {
      function _class(_ref) {
        var config = _ref.config;

        _classCallCheck(this, _class);

        this._config = config;

        this._iv = '0123456789012345';

        this._allowedKeyEncodings = ['hex', 'utf8', 'base64', 'binary'];
        this._allowedKeyLengths = [128, 256];
        this._allowedModes = ['ecb', 'cbc'];

        this._defaultOptions = {
          encryptKey: true,
          keyEncoding: 'utf8',
          keyLength: 256,
          mode: 'cbc'
        };
      }

      _createClass(_class, [{
        key: 'HMACSHA256',
        value: function HMACSHA256(data) {
          var hash = _hmacSha2.default.HmacSHA256(data, this._config.secretKey);
          return hash.toString(_hmacSha2.default.enc.Base64);
        }
      }, {
        key: 'SHA256',
        value: function SHA256(s) {
          return _hmacSha2.default.SHA256(s).toString(_hmacSha2.default.enc.Hex);
        }
      }, {
        key: '_parseOptions',
        value: function _parseOptions(incomingOptions) {
          var options = incomingOptions || {};
          if (!options.hasOwnProperty('encryptKey')) options.encryptKey = this._defaultOptions.encryptKey;
          if (!options.hasOwnProperty('keyEncoding')) options.keyEncoding = this._defaultOptions.keyEncoding;
          if (!options.hasOwnProperty('keyLength')) options.keyLength = this._defaultOptions.keyLength;
          if (!options.hasOwnProperty('mode')) options.mode = this._defaultOptions.mode;

          if (this._allowedKeyEncodings.indexOf(options.keyEncoding.toLowerCase()) === -1) {
            options.keyEncoding = this._defaultOptions.keyEncoding;
          }

          if (this._allowedKeyLengths.indexOf(parseInt(options.keyLength, 10)) === -1) {
            options.keyLength = this._defaultOptions.keyLength;
          }

          if (this._allowedModes.indexOf(options.mode.toLowerCase()) === -1) {
            options.mode = this._defaultOptions.mode;
          }

          return options;
        }
      }, {
        key: '_decodeKey',
        value: function _decodeKey(key, options) {
          if (options.keyEncoding === 'base64') {
            return _hmacSha2.default.enc.Base64.parse(key);
          } else if (options.keyEncoding === 'hex') {
            return _hmacSha2.default.enc.Hex.parse(key);
          } else {
            return key;
          }
        }
      }, {
        key: '_getPaddedKey',
        value: function _getPaddedKey(key, options) {
          key = this._decodeKey(key, options);
          if (options.encryptKey) {
            return _hmacSha2.default.enc.Utf8.parse(this.SHA256(key).slice(0, 32));
          } else {
            return key;
          }
        }
      }, {
        key: '_getMode',
        value: function _getMode(options) {
          if (options.mode === 'ecb') {
            return _hmacSha2.default.mode.ECB;
          } else {
            return _hmacSha2.default.mode.CBC;
          }
        }
      }, {
        key: '_getIV',
        value: function _getIV(options) {
          return options.mode === 'cbc' ? _hmacSha2.default.enc.Utf8.parse(this._iv) : null;
        }
      }, {
        key: 'encrypt',
        value: function encrypt(data, customCipherKey, options) {
          if (!customCipherKey && !this._config.cipherKey) return data;
          options = this._parseOptions(options);
          var iv = this._getIV(options);
          var mode = this._getMode(options);
          var cipherKey = this._getPaddedKey(customCipherKey || this._config.cipherKey, options);
          var encryptedHexArray = _hmacSha2.default.AES.encrypt(data, cipherKey, { iv: iv, mode: mode }).ciphertext;
          var base64Encrypted = encryptedHexArray.toString(_hmacSha2.default.enc.Base64);
          return base64Encrypted || data;
        }
      }, {
        key: 'decrypt',
        value: function decrypt(data, customCipherKey, options) {
          if (!customCipherKey && !this._config.cipherKey) return data;
          options = this._parseOptions(options);
          var iv = this._getIV(options);
          var mode = this._getMode(options);
          var cipherKey = this._getPaddedKey(customCipherKey || this._config.cipherKey, options);
          try {
            var ciphertext = _hmacSha2.default.enc.Base64.parse(data);
            var plainJSON = _hmacSha2.default.AES.decrypt({ ciphertext: ciphertext }, cipherKey, { iv: iv, mode: mode }).toString(_hmacSha2.default.enc.Utf8);
            var plaintext = JSON.parse(plainJSON);
            return plaintext;
          } catch (e) {
            return null;
          }
        }
      }]);

      return _class;
    }();

    exports.default = _class;
    module.exports = exports['default'];

/***/ },
/* 10 */
/***/ function(module, exports) {

    "use strict";

    var CryptoJS = CryptoJS || function (h, s) {
      var f = {},
          g = f.lib = {},
          q = function q() {},
          m = g.Base = { extend: function extend(a) {
          q.prototype = this;var c = new q();a && c.mixIn(a);c.hasOwnProperty("init") || (c.init = function () {
            c.$super.init.apply(this, arguments);
          });c.init.prototype = c;c.$super = this;return c;
        }, create: function create() {
          var a = this.extend();a.init.apply(a, arguments);return a;
        }, init: function init() {}, mixIn: function mixIn(a) {
          for (var c in a) {
            a.hasOwnProperty(c) && (this[c] = a[c]);
          }a.hasOwnProperty("toString") && (this.toString = a.toString);
        }, clone: function clone() {
          return this.init.prototype.extend(this);
        } },
          r = g.WordArray = m.extend({ init: function init(a, c) {
          a = this.words = a || [];this.sigBytes = c != s ? c : 4 * a.length;
        }, toString: function toString(a) {
          return (a || k).stringify(this);
        }, concat: function concat(a) {
          var c = this.words,
              d = a.words,
              b = this.sigBytes;a = a.sigBytes;this.clamp();if (b % 4) for (var e = 0; e < a; e++) {
            c[b + e >>> 2] |= (d[e >>> 2] >>> 24 - 8 * (e % 4) & 255) << 24 - 8 * ((b + e) % 4);
          } else if (65535 < d.length) for (e = 0; e < a; e += 4) {
            c[b + e >>> 2] = d[e >>> 2];
          } else c.push.apply(c, d);this.sigBytes += a;return this;
        }, clamp: function clamp() {
          var a = this.words,
              c = this.sigBytes;a[c >>> 2] &= 4294967295 << 32 - 8 * (c % 4);a.length = h.ceil(c / 4);
        }, clone: function clone() {
          var a = m.clone.call(this);a.words = this.words.slice(0);return a;
        }, random: function random(a) {
          for (var c = [], d = 0; d < a; d += 4) {
            c.push(4294967296 * h.random() | 0);
          }return new r.init(c, a);
        } }),
          l = f.enc = {},
          k = l.Hex = { stringify: function stringify(a) {
          var c = a.words;a = a.sigBytes;for (var d = [], b = 0; b < a; b++) {
            var e = c[b >>> 2] >>> 24 - 8 * (b % 4) & 255;d.push((e >>> 4).toString(16));d.push((e & 15).toString(16));
          }return d.join("");
        }, parse: function parse(a) {
          for (var c = a.length, d = [], b = 0; b < c; b += 2) {
            d[b >>> 3] |= parseInt(a.substr(b, 2), 16) << 24 - 4 * (b % 8);
          }return new r.init(d, c / 2);
        } },
          n = l.Latin1 = { stringify: function stringify(a) {
          var c = a.words;a = a.sigBytes;for (var d = [], b = 0; b < a; b++) {
            d.push(String.fromCharCode(c[b >>> 2] >>> 24 - 8 * (b % 4) & 255));
          }return d.join("");
        }, parse: function parse(a) {
          for (var c = a.length, d = [], b = 0; b < c; b++) {
            d[b >>> 2] |= (a.charCodeAt(b) & 255) << 24 - 8 * (b % 4);
          }return new r.init(d, c);
        } },
          j = l.Utf8 = { stringify: function stringify(a) {
          try {
            return decodeURIComponent(escape(n.stringify(a)));
          } catch (c) {
            throw Error("Malformed UTF-8 data");
          }
        }, parse: function parse(a) {
          return n.parse(unescape(encodeURIComponent(a)));
        } },
          u = g.BufferedBlockAlgorithm = m.extend({ reset: function reset() {
          this._data = new r.init();this._nDataBytes = 0;
        }, _append: function _append(a) {
          "string" == typeof a && (a = j.parse(a));this._data.concat(a);this._nDataBytes += a.sigBytes;
        }, _process: function _process(a) {
          var c = this._data,
              d = c.words,
              b = c.sigBytes,
              e = this.blockSize,
              f = b / (4 * e),
              f = a ? h.ceil(f) : h.max((f | 0) - this._minBufferSize, 0);a = f * e;b = h.min(4 * a, b);if (a) {
            for (var g = 0; g < a; g += e) {
              this._doProcessBlock(d, g);
            }g = d.splice(0, a);c.sigBytes -= b;
          }return new r.init(g, b);
        }, clone: function clone() {
          var a = m.clone.call(this);
          a._data = this._data.clone();return a;
        }, _minBufferSize: 0 });g.Hasher = u.extend({ cfg: m.extend(), init: function init(a) {
          this.cfg = this.cfg.extend(a);this.reset();
        }, reset: function reset() {
          u.reset.call(this);this._doReset();
        }, update: function update(a) {
          this._append(a);this._process();return this;
        }, finalize: function finalize(a) {
          a && this._append(a);return this._doFinalize();
        }, blockSize: 16, _createHelper: function _createHelper(a) {
          return function (c, d) {
            return new a.init(d).finalize(c);
          };
        }, _createHmacHelper: function _createHmacHelper(a) {
          return function (c, d) {
            return new t.HMAC.init(a, d).finalize(c);
          };
        } });var t = f.algo = {};return f;
    }(Math);

    (function (h) {
      for (var s = CryptoJS, f = s.lib, g = f.WordArray, q = f.Hasher, f = s.algo, m = [], r = [], l = function l(a) {
        return 4294967296 * (a - (a | 0)) | 0;
      }, k = 2, n = 0; 64 > n;) {
        var j;a: {
          j = k;for (var u = h.sqrt(j), t = 2; t <= u; t++) {
            if (!(j % t)) {
              j = !1;break a;
            }
          }j = !0;
        }j && (8 > n && (m[n] = l(h.pow(k, 0.5))), r[n] = l(h.pow(k, 1 / 3)), n++);k++;
      }var a = [],
          f = f.SHA256 = q.extend({ _doReset: function _doReset() {
          this._hash = new g.init(m.slice(0));
        }, _doProcessBlock: function _doProcessBlock(c, d) {
          for (var b = this._hash.words, e = b[0], f = b[1], g = b[2], j = b[3], h = b[4], m = b[5], n = b[6], q = b[7], p = 0; 64 > p; p++) {
            if (16 > p) a[p] = c[d + p] | 0;else {
              var k = a[p - 15],
                  l = a[p - 2];a[p] = ((k << 25 | k >>> 7) ^ (k << 14 | k >>> 18) ^ k >>> 3) + a[p - 7] + ((l << 15 | l >>> 17) ^ (l << 13 | l >>> 19) ^ l >>> 10) + a[p - 16];
            }k = q + ((h << 26 | h >>> 6) ^ (h << 21 | h >>> 11) ^ (h << 7 | h >>> 25)) + (h & m ^ ~h & n) + r[p] + a[p];l = ((e << 30 | e >>> 2) ^ (e << 19 | e >>> 13) ^ (e << 10 | e >>> 22)) + (e & f ^ e & g ^ f & g);q = n;n = m;m = h;h = j + k | 0;j = g;g = f;f = e;e = k + l | 0;
          }b[0] = b[0] + e | 0;b[1] = b[1] + f | 0;b[2] = b[2] + g | 0;b[3] = b[3] + j | 0;b[4] = b[4] + h | 0;b[5] = b[5] + m | 0;b[6] = b[6] + n | 0;b[7] = b[7] + q | 0;
        }, _doFinalize: function _doFinalize() {
          var a = this._data,
              d = a.words,
              b = 8 * this._nDataBytes,
              e = 8 * a.sigBytes;
          d[e >>> 5] |= 128 << 24 - e % 32;d[(e + 64 >>> 9 << 4) + 14] = h.floor(b / 4294967296);d[(e + 64 >>> 9 << 4) + 15] = b;a.sigBytes = 4 * d.length;this._process();return this._hash;
        }, clone: function clone() {
          var a = q.clone.call(this);a._hash = this._hash.clone();return a;
        } });s.SHA256 = q._createHelper(f);s.HmacSHA256 = q._createHmacHelper(f);
    })(Math);

    (function () {
      var h = CryptoJS,
          s = h.enc.Utf8;h.algo.HMAC = h.lib.Base.extend({ init: function init(f, g) {
          f = this._hasher = new f.init();"string" == typeof g && (g = s.parse(g));var h = f.blockSize,
              m = 4 * h;g.sigBytes > m && (g = f.finalize(g));g.clamp();for (var r = this._oKey = g.clone(), l = this._iKey = g.clone(), k = r.words, n = l.words, j = 0; j < h; j++) {
            k[j] ^= 1549556828, n[j] ^= 909522486;
          }r.sigBytes = l.sigBytes = m;this.reset();
        }, reset: function reset() {
          var f = this._hasher;f.reset();f.update(this._iKey);
        }, update: function update(f) {
          this._hasher.update(f);return this;
        }, finalize: function finalize(f) {
          var g = this._hasher;f = g.finalize(f);g.reset();return g.finalize(this._oKey.clone().concat(f));
        } });
    })();

    (function () {
      var u = CryptoJS,
          p = u.lib.WordArray;u.enc.Base64 = { stringify: function stringify(d) {
          var l = d.words,
              p = d.sigBytes,
              t = this._map;d.clamp();d = [];for (var r = 0; r < p; r += 3) {
            for (var w = (l[r >>> 2] >>> 24 - 8 * (r % 4) & 255) << 16 | (l[r + 1 >>> 2] >>> 24 - 8 * ((r + 1) % 4) & 255) << 8 | l[r + 2 >>> 2] >>> 24 - 8 * ((r + 2) % 4) & 255, v = 0; 4 > v && r + 0.75 * v < p; v++) {
              d.push(t.charAt(w >>> 6 * (3 - v) & 63));
            }
          }if (l = t.charAt(64)) for (; d.length % 4;) {
            d.push(l);
          }return d.join("");
        }, parse: function parse(d) {
          var l = d.length,
              s = this._map,
              t = s.charAt(64);t && (t = d.indexOf(t), -1 != t && (l = t));for (var t = [], r = 0, w = 0; w < l; w++) {
            if (w % 4) {
              var v = s.indexOf(d.charAt(w - 1)) << 2 * (w % 4),
                  b = s.indexOf(d.charAt(w)) >>> 6 - 2 * (w % 4);t[r >>> 2] |= (v | b) << 24 - 8 * (r % 4);r++;
            }
          }return p.create(t, r);
        }, _map: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=" };
    })();

    (function (u) {
      function p(b, n, a, c, e, j, k) {
        b = b + (n & a | ~n & c) + e + k;return (b << j | b >>> 32 - j) + n;
      }function d(b, n, a, c, e, j, k) {
        b = b + (n & c | a & ~c) + e + k;return (b << j | b >>> 32 - j) + n;
      }function l(b, n, a, c, e, j, k) {
        b = b + (n ^ a ^ c) + e + k;return (b << j | b >>> 32 - j) + n;
      }function s(b, n, a, c, e, j, k) {
        b = b + (a ^ (n | ~c)) + e + k;return (b << j | b >>> 32 - j) + n;
      }for (var t = CryptoJS, r = t.lib, w = r.WordArray, v = r.Hasher, r = t.algo, b = [], x = 0; 64 > x; x++) {
        b[x] = 4294967296 * u.abs(u.sin(x + 1)) | 0;
      }r = r.MD5 = v.extend({ _doReset: function _doReset() {
          this._hash = new w.init([1732584193, 4023233417, 2562383102, 271733878]);
        },
        _doProcessBlock: function _doProcessBlock(q, n) {
          for (var a = 0; 16 > a; a++) {
            var c = n + a,
                e = q[c];q[c] = (e << 8 | e >>> 24) & 16711935 | (e << 24 | e >>> 8) & 4278255360;
          }var a = this._hash.words,
              c = q[n + 0],
              e = q[n + 1],
              j = q[n + 2],
              k = q[n + 3],
              z = q[n + 4],
              r = q[n + 5],
              t = q[n + 6],
              w = q[n + 7],
              v = q[n + 8],
              A = q[n + 9],
              B = q[n + 10],
              C = q[n + 11],
              u = q[n + 12],
              D = q[n + 13],
              E = q[n + 14],
              x = q[n + 15],
              f = a[0],
              m = a[1],
              g = a[2],
              h = a[3],
              f = p(f, m, g, h, c, 7, b[0]),
              h = p(h, f, m, g, e, 12, b[1]),
              g = p(g, h, f, m, j, 17, b[2]),
              m = p(m, g, h, f, k, 22, b[3]),
              f = p(f, m, g, h, z, 7, b[4]),
              h = p(h, f, m, g, r, 12, b[5]),
              g = p(g, h, f, m, t, 17, b[6]),
              m = p(m, g, h, f, w, 22, b[7]),
              f = p(f, m, g, h, v, 7, b[8]),
              h = p(h, f, m, g, A, 12, b[9]),
              g = p(g, h, f, m, B, 17, b[10]),
              m = p(m, g, h, f, C, 22, b[11]),
              f = p(f, m, g, h, u, 7, b[12]),
              h = p(h, f, m, g, D, 12, b[13]),
              g = p(g, h, f, m, E, 17, b[14]),
              m = p(m, g, h, f, x, 22, b[15]),
              f = d(f, m, g, h, e, 5, b[16]),
              h = d(h, f, m, g, t, 9, b[17]),
              g = d(g, h, f, m, C, 14, b[18]),
              m = d(m, g, h, f, c, 20, b[19]),
              f = d(f, m, g, h, r, 5, b[20]),
              h = d(h, f, m, g, B, 9, b[21]),
              g = d(g, h, f, m, x, 14, b[22]),
              m = d(m, g, h, f, z, 20, b[23]),
              f = d(f, m, g, h, A, 5, b[24]),
              h = d(h, f, m, g, E, 9, b[25]),
              g = d(g, h, f, m, k, 14, b[26]),
              m = d(m, g, h, f, v, 20, b[27]),
              f = d(f, m, g, h, D, 5, b[28]),
              h = d(h, f, m, g, j, 9, b[29]),
              g = d(g, h, f, m, w, 14, b[30]),
              m = d(m, g, h, f, u, 20, b[31]),
              f = l(f, m, g, h, r, 4, b[32]),
              h = l(h, f, m, g, v, 11, b[33]),
              g = l(g, h, f, m, C, 16, b[34]),
              m = l(m, g, h, f, E, 23, b[35]),
              f = l(f, m, g, h, e, 4, b[36]),
              h = l(h, f, m, g, z, 11, b[37]),
              g = l(g, h, f, m, w, 16, b[38]),
              m = l(m, g, h, f, B, 23, b[39]),
              f = l(f, m, g, h, D, 4, b[40]),
              h = l(h, f, m, g, c, 11, b[41]),
              g = l(g, h, f, m, k, 16, b[42]),
              m = l(m, g, h, f, t, 23, b[43]),
              f = l(f, m, g, h, A, 4, b[44]),
              h = l(h, f, m, g, u, 11, b[45]),
              g = l(g, h, f, m, x, 16, b[46]),
              m = l(m, g, h, f, j, 23, b[47]),
              f = s(f, m, g, h, c, 6, b[48]),
              h = s(h, f, m, g, w, 10, b[49]),
              g = s(g, h, f, m, E, 15, b[50]),
              m = s(m, g, h, f, r, 21, b[51]),
              f = s(f, m, g, h, u, 6, b[52]),
              h = s(h, f, m, g, k, 10, b[53]),
              g = s(g, h, f, m, B, 15, b[54]),
              m = s(m, g, h, f, e, 21, b[55]),
              f = s(f, m, g, h, v, 6, b[56]),
              h = s(h, f, m, g, x, 10, b[57]),
              g = s(g, h, f, m, t, 15, b[58]),
              m = s(m, g, h, f, D, 21, b[59]),
              f = s(f, m, g, h, z, 6, b[60]),
              h = s(h, f, m, g, C, 10, b[61]),
              g = s(g, h, f, m, j, 15, b[62]),
              m = s(m, g, h, f, A, 21, b[63]);a[0] = a[0] + f | 0;a[1] = a[1] + m | 0;a[2] = a[2] + g | 0;a[3] = a[3] + h | 0;
        }, _doFinalize: function _doFinalize() {
          var b = this._data,
              n = b.words,
              a = 8 * this._nDataBytes,
              c = 8 * b.sigBytes;n[c >>> 5] |= 128 << 24 - c % 32;var e = u.floor(a / 4294967296);n[(c + 64 >>> 9 << 4) + 15] = (e << 8 | e >>> 24) & 16711935 | (e << 24 | e >>> 8) & 4278255360;n[(c + 64 >>> 9 << 4) + 14] = (a << 8 | a >>> 24) & 16711935 | (a << 24 | a >>> 8) & 4278255360;b.sigBytes = 4 * (n.length + 1);this._process();b = this._hash;n = b.words;for (a = 0; 4 > a; a++) {
            c = n[a], n[a] = (c << 8 | c >>> 24) & 16711935 | (c << 24 | c >>> 8) & 4278255360;
          }return b;
        }, clone: function clone() {
          var b = v.clone.call(this);b._hash = this._hash.clone();return b;
        } });t.MD5 = v._createHelper(r);t.HmacMD5 = v._createHmacHelper(r);
    })(Math);
    (function () {
      var u = CryptoJS,
          p = u.lib,
          d = p.Base,
          l = p.WordArray,
          p = u.algo,
          s = p.EvpKDF = d.extend({ cfg: d.extend({ keySize: 4, hasher: p.MD5, iterations: 1 }), init: function init(d) {
          this.cfg = this.cfg.extend(d);
        }, compute: function compute(d, r) {
          for (var p = this.cfg, s = p.hasher.create(), b = l.create(), u = b.words, q = p.keySize, p = p.iterations; u.length < q;) {
            n && s.update(n);var n = s.update(d).finalize(r);s.reset();for (var a = 1; a < p; a++) {
              n = s.finalize(n), s.reset();
            }b.concat(n);
          }b.sigBytes = 4 * q;return b;
        } });u.EvpKDF = function (d, l, p) {
        return s.create(p).compute(d, l);
      };
    })();

    CryptoJS.lib.Cipher || function (u) {
      var p = CryptoJS,
          d = p.lib,
          l = d.Base,
          s = d.WordArray,
          t = d.BufferedBlockAlgorithm,
          r = p.enc.Base64,
          w = p.algo.EvpKDF,
          v = d.Cipher = t.extend({ cfg: l.extend(), createEncryptor: function createEncryptor(e, a) {
          return this.create(this._ENC_XFORM_MODE, e, a);
        }, createDecryptor: function createDecryptor(e, a) {
          return this.create(this._DEC_XFORM_MODE, e, a);
        }, init: function init(e, a, b) {
          this.cfg = this.cfg.extend(b);this._xformMode = e;this._key = a;this.reset();
        }, reset: function reset() {
          t.reset.call(this);this._doReset();
        }, process: function process(e) {
          this._append(e);return this._process();
        },
        finalize: function finalize(e) {
          e && this._append(e);return this._doFinalize();
        }, keySize: 4, ivSize: 4, _ENC_XFORM_MODE: 1, _DEC_XFORM_MODE: 2, _createHelper: function _createHelper(e) {
          return { encrypt: function encrypt(b, k, d) {
              return ("string" == typeof k ? c : a).encrypt(e, b, k, d);
            }, decrypt: function decrypt(b, k, d) {
              return ("string" == typeof k ? c : a).decrypt(e, b, k, d);
            } };
        } });d.StreamCipher = v.extend({ _doFinalize: function _doFinalize() {
          return this._process(!0);
        }, blockSize: 1 });var b = p.mode = {},
          x = function x(e, a, b) {
        var c = this._iv;c ? this._iv = u : c = this._prevBlock;for (var d = 0; d < b; d++) {
          e[a + d] ^= c[d];
        }
      },
          q = (d.BlockCipherMode = l.extend({ createEncryptor: function createEncryptor(e, a) {
          return this.Encryptor.create(e, a);
        }, createDecryptor: function createDecryptor(e, a) {
          return this.Decryptor.create(e, a);
        }, init: function init(e, a) {
          this._cipher = e;this._iv = a;
        } })).extend();q.Encryptor = q.extend({ processBlock: function processBlock(e, a) {
          var b = this._cipher,
              c = b.blockSize;x.call(this, e, a, c);b.encryptBlock(e, a);this._prevBlock = e.slice(a, a + c);
        } });q.Decryptor = q.extend({ processBlock: function processBlock(e, a) {
          var b = this._cipher,
              c = b.blockSize,
              d = e.slice(a, a + c);b.decryptBlock(e, a);x.call(this, e, a, c);this._prevBlock = d;
        } });b = b.CBC = q;q = (p.pad = {}).Pkcs7 = { pad: function pad(a, b) {
          for (var c = 4 * b, c = c - a.sigBytes % c, d = c << 24 | c << 16 | c << 8 | c, l = [], n = 0; n < c; n += 4) {
            l.push(d);
          }c = s.create(l, c);a.concat(c);
        }, unpad: function unpad(a) {
          a.sigBytes -= a.words[a.sigBytes - 1 >>> 2] & 255;
        } };d.BlockCipher = v.extend({ cfg: v.cfg.extend({ mode: b, padding: q }), reset: function reset() {
          v.reset.call(this);var a = this.cfg,
              b = a.iv,
              a = a.mode;if (this._xformMode == this._ENC_XFORM_MODE) var c = a.createEncryptor;else c = a.createDecryptor, this._minBufferSize = 1;this._mode = c.call(a, this, b && b.words);
        }, _doProcessBlock: function _doProcessBlock(a, b) {
          this._mode.processBlock(a, b);
        }, _doFinalize: function _doFinalize() {
          var a = this.cfg.padding;if (this._xformMode == this._ENC_XFORM_MODE) {
            a.pad(this._data, this.blockSize);var b = this._process(!0);
          } else b = this._process(!0), a.unpad(b);return b;
        }, blockSize: 4 });var n = d.CipherParams = l.extend({ init: function init(a) {
          this.mixIn(a);
        }, toString: function toString(a) {
          return (a || this.formatter).stringify(this);
        } }),
          b = (p.format = {}).OpenSSL = { stringify: function stringify(a) {
          var b = a.ciphertext;a = a.salt;return (a ? s.create([1398893684, 1701076831]).concat(a).concat(b) : b).toString(r);
        }, parse: function parse(a) {
          a = r.parse(a);var b = a.words;if (1398893684 == b[0] && 1701076831 == b[1]) {
            var c = s.create(b.slice(2, 4));b.splice(0, 4);a.sigBytes -= 16;
          }return n.create({ ciphertext: a, salt: c });
        } },
          a = d.SerializableCipher = l.extend({ cfg: l.extend({ format: b }), encrypt: function encrypt(a, b, c, d) {
          d = this.cfg.extend(d);var l = a.createEncryptor(c, d);b = l.finalize(b);l = l.cfg;return n.create({ ciphertext: b, key: c, iv: l.iv, algorithm: a, mode: l.mode, padding: l.padding, blockSize: a.blockSize, formatter: d.format });
        },
        decrypt: function decrypt(a, b, c, d) {
          d = this.cfg.extend(d);b = this._parse(b, d.format);return a.createDecryptor(c, d).finalize(b.ciphertext);
        }, _parse: function _parse(a, b) {
          return "string" == typeof a ? b.parse(a, this) : a;
        } }),
          p = (p.kdf = {}).OpenSSL = { execute: function execute(a, b, c, d) {
          d || (d = s.random(8));a = w.create({ keySize: b + c }).compute(a, d);c = s.create(a.words.slice(b), 4 * c);a.sigBytes = 4 * b;return n.create({ key: a, iv: c, salt: d });
        } },
          c = d.PasswordBasedCipher = a.extend({ cfg: a.cfg.extend({ kdf: p }), encrypt: function encrypt(b, c, d, l) {
          l = this.cfg.extend(l);d = l.kdf.execute(d, b.keySize, b.ivSize);l.iv = d.iv;b = a.encrypt.call(this, b, c, d.key, l);b.mixIn(d);return b;
        }, decrypt: function decrypt(b, c, d, l) {
          l = this.cfg.extend(l);c = this._parse(c, l.format);d = l.kdf.execute(d, b.keySize, b.ivSize, c.salt);l.iv = d.iv;return a.decrypt.call(this, b, c, d.key, l);
        } });
    }();

    (function () {
      for (var u = CryptoJS, p = u.lib.BlockCipher, d = u.algo, l = [], s = [], t = [], r = [], w = [], v = [], b = [], x = [], q = [], n = [], a = [], c = 0; 256 > c; c++) {
        a[c] = 128 > c ? c << 1 : c << 1 ^ 283;
      }for (var e = 0, j = 0, c = 0; 256 > c; c++) {
        var k = j ^ j << 1 ^ j << 2 ^ j << 3 ^ j << 4,
            k = k >>> 8 ^ k & 255 ^ 99;l[e] = k;s[k] = e;var z = a[e],
            F = a[z],
            G = a[F],
            y = 257 * a[k] ^ 16843008 * k;t[e] = y << 24 | y >>> 8;r[e] = y << 16 | y >>> 16;w[e] = y << 8 | y >>> 24;v[e] = y;y = 16843009 * G ^ 65537 * F ^ 257 * z ^ 16843008 * e;b[k] = y << 24 | y >>> 8;x[k] = y << 16 | y >>> 16;q[k] = y << 8 | y >>> 24;n[k] = y;e ? (e = z ^ a[a[a[G ^ z]]], j ^= a[a[j]]) : e = j = 1;
      }var H = [0, 1, 2, 4, 8, 16, 32, 64, 128, 27, 54],
          d = d.AES = p.extend({ _doReset: function _doReset() {
          for (var a = this._key, c = a.words, d = a.sigBytes / 4, a = 4 * ((this._nRounds = d + 6) + 1), e = this._keySchedule = [], j = 0; j < a; j++) {
            if (j < d) e[j] = c[j];else {
              var k = e[j - 1];j % d ? 6 < d && 4 == j % d && (k = l[k >>> 24] << 24 | l[k >>> 16 & 255] << 16 | l[k >>> 8 & 255] << 8 | l[k & 255]) : (k = k << 8 | k >>> 24, k = l[k >>> 24] << 24 | l[k >>> 16 & 255] << 16 | l[k >>> 8 & 255] << 8 | l[k & 255], k ^= H[j / d | 0] << 24);e[j] = e[j - d] ^ k;
            }
          }c = this._invKeySchedule = [];for (d = 0; d < a; d++) {
            j = a - d, k = d % 4 ? e[j] : e[j - 4], c[d] = 4 > d || 4 >= j ? k : b[l[k >>> 24]] ^ x[l[k >>> 16 & 255]] ^ q[l[k >>> 8 & 255]] ^ n[l[k & 255]];
          }
        }, encryptBlock: function encryptBlock(a, b) {
          this._doCryptBlock(a, b, this._keySchedule, t, r, w, v, l);
        }, decryptBlock: function decryptBlock(a, c) {
          var d = a[c + 1];a[c + 1] = a[c + 3];a[c + 3] = d;this._doCryptBlock(a, c, this._invKeySchedule, b, x, q, n, s);d = a[c + 1];a[c + 1] = a[c + 3];a[c + 3] = d;
        }, _doCryptBlock: function _doCryptBlock(a, b, c, d, e, j, l, f) {
          for (var m = this._nRounds, g = a[b] ^ c[0], h = a[b + 1] ^ c[1], k = a[b + 2] ^ c[2], n = a[b + 3] ^ c[3], p = 4, r = 1; r < m; r++) {
            var q = d[g >>> 24] ^ e[h >>> 16 & 255] ^ j[k >>> 8 & 255] ^ l[n & 255] ^ c[p++],
                s = d[h >>> 24] ^ e[k >>> 16 & 255] ^ j[n >>> 8 & 255] ^ l[g & 255] ^ c[p++],
                t = d[k >>> 24] ^ e[n >>> 16 & 255] ^ j[g >>> 8 & 255] ^ l[h & 255] ^ c[p++],
                n = d[n >>> 24] ^ e[g >>> 16 & 255] ^ j[h >>> 8 & 255] ^ l[k & 255] ^ c[p++],
                g = q,
                h = s,
                k = t;
          }q = (f[g >>> 24] << 24 | f[h >>> 16 & 255] << 16 | f[k >>> 8 & 255] << 8 | f[n & 255]) ^ c[p++];s = (f[h >>> 24] << 24 | f[k >>> 16 & 255] << 16 | f[n >>> 8 & 255] << 8 | f[g & 255]) ^ c[p++];t = (f[k >>> 24] << 24 | f[n >>> 16 & 255] << 16 | f[g >>> 8 & 255] << 8 | f[h & 255]) ^ c[p++];n = (f[n >>> 24] << 24 | f[g >>> 16 & 255] << 16 | f[h >>> 8 & 255] << 8 | f[k & 255]) ^ c[p++];a[b] = q;a[b + 1] = s;a[b + 2] = t;a[b + 3] = n;
        }, keySize: 8 });u.AES = p._createHelper(d);
    })();

    CryptoJS.mode.ECB = function () {
      var ECB = CryptoJS.lib.BlockCipherMode.extend();

      ECB.Encryptor = ECB.extend({
        processBlock: function processBlock(words, offset) {
          this._cipher.encryptBlock(words, offset);
        }
      });

      ECB.Decryptor = ECB.extend({
        processBlock: function processBlock(words, offset) {
          this._cipher.decryptBlock(words, offset);
        }
      });

      return ECB;
    }();

    module.exports = CryptoJS;

/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });

    var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

    var _cryptography = __webpack_require__(9);

    var _cryptography2 = _interopRequireDefault(_cryptography);

    var _config = __webpack_require__(7);

    var _config2 = _interopRequireDefault(_config);

    var _listener_manager = __webpack_require__(12);

    var _listener_manager2 = _interopRequireDefault(_listener_manager);

    var _reconnection_manager = __webpack_require__(14);

    var _reconnection_manager2 = _interopRequireDefault(_reconnection_manager);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    var _flow_interfaces = __webpack_require__(8);

    var _categories = __webpack_require__(13);

    var _categories2 = _interopRequireDefault(_categories);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

    var _class = function () {
      function _class(_ref) {
        var subscribeEndpoint = _ref.subscribeEndpoint,
            leaveEndpoint = _ref.leaveEndpoint,
            heartbeatEndpoint = _ref.heartbeatEndpoint,
            setStateEndpoint = _ref.setStateEndpoint,
            timeEndpoint = _ref.timeEndpoint,
            config = _ref.config,
            crypto = _ref.crypto,
            listenerManager = _ref.listenerManager;

        _classCallCheck(this, _class);

        this._listenerManager = listenerManager;
        this._config = config;

        this._leaveEndpoint = leaveEndpoint;
        this._heartbeatEndpoint = heartbeatEndpoint;
        this._setStateEndpoint = setStateEndpoint;
        this._subscribeEndpoint = subscribeEndpoint;

        this._crypto = crypto;

        this._channels = {};
        this._presenceChannels = {};

        this._channelGroups = {};
        this._presenceChannelGroups = {};

        this._pendingChannelSubscriptions = [];
        this._pendingChannelGroupSubscriptions = [];

        this._currentTimetoken = 0;
        this._lastTimetoken = 0;

        this._subscriptionStatusAnnounced = false;

        this._reconnectionManager = new _reconnection_manager2.default({ timeEndpoint: timeEndpoint });
      }

      _createClass(_class, [{
        key: 'adaptStateChange',
        value: function adaptStateChange(args, callback) {
          var _this = this;

          var state = args.state,
              _args$channels = args.channels,
              channels = _args$channels === undefined ? [] : _args$channels,
              _args$channelGroups = args.channelGroups,
              channelGroups = _args$channelGroups === undefined ? [] : _args$channelGroups;


          channels.forEach(function (channel) {
            if (channel in _this._channels) _this._channels[channel].state = state;
          });

          channelGroups.forEach(function (channelGroup) {
            if (channelGroup in _this._channelGroups) _this._channelGroups[channelGroup].state = state;
          });

          return this._setStateEndpoint({ state: state, channels: channels, channelGroups: channelGroups }, callback);
        }
      }, {
        key: 'adaptSubscribeChange',
        value: function adaptSubscribeChange(args) {
          var _this2 = this;

          var timetoken = args.timetoken,
              _args$channels2 = args.channels,
              channels = _args$channels2 === undefined ? [] : _args$channels2,
              _args$channelGroups2 = args.channelGroups,
              channelGroups = _args$channelGroups2 === undefined ? [] : _args$channelGroups2,
              _args$withPresence = args.withPresence,
              withPresence = _args$withPresence === undefined ? false : _args$withPresence;


          if (!this._config.subscribeKey || this._config.subscribeKey === '') {
            if (console && console.log) console.log('subscribe key missing; aborting subscribe');
            return;
          }

          if (timetoken) {
            this._lastTimetoken = this._currentTimetoken;
            this._currentTimetoken = timetoken;
          }

          channels.forEach(function (channel) {
            _this2._channels[channel] = { state: {} };
            if (withPresence) _this2._presenceChannels[channel] = {};

            _this2._pendingChannelSubscriptions.push(channel);
          });

          channelGroups.forEach(function (channelGroup) {
            _this2._channelGroups[channelGroup] = { state: {} };
            if (withPresence) _this2._presenceChannelGroups[channelGroup] = {};

            _this2._pendingChannelGroupSubscriptions.push(channelGroup);
          });

          this._subscriptionStatusAnnounced = false;
          this.reconnect();
        }
      }, {
        key: 'adaptUnsubscribeChange',
        value: function adaptUnsubscribeChange(args, isOffline) {
          var _this3 = this;

          var _args$channels3 = args.channels,
              channels = _args$channels3 === undefined ? [] : _args$channels3,
              _args$channelGroups3 = args.channelGroups,
              channelGroups = _args$channelGroups3 === undefined ? [] : _args$channelGroups3;


          channels.forEach(function (channel) {
            if (channel in _this3._channels) delete _this3._channels[channel];
            if (channel in _this3._presenceChannels) delete _this3._presenceChannels[channel];
          });

          channelGroups.forEach(function (channelGroup) {
            if (channelGroup in _this3._channelGroups) delete _this3._channelGroups[channelGroup];
            if (channelGroup in _this3._presenceChannelGroups) delete _this3._channelGroups[channelGroup];
          });

          if (this._config.suppressLeaveEvents === false && !isOffline) {
            this._leaveEndpoint({ channels: channels, channelGroups: channelGroups }, function (status) {
              status.affectedChannels = channels;
              status.affectedChannelGroups = channelGroups;
              status.currentTimetoken = _this3._currentTimetoken;
              status.lastTimetoken = _this3._lastTimetoken;
              _this3._listenerManager.announceStatus(status);
            });
          }

          if (Object.keys(this._channels).length === 0 && Object.keys(this._presenceChannels).length === 0 && Object.keys(this._channelGroups).length === 0 && Object.keys(this._presenceChannelGroups).length === 0) {
            this._lastTimetoken = 0;
            this._currentTimetoken = 0;
            this._region = null;
            this._reconnectionManager.stopPolling();
          }

          this.reconnect();
        }
      }, {
        key: 'unsubscribeAll',
        value: function unsubscribeAll(isOffline) {
          this.adaptUnsubscribeChange({ channels: this.getSubscribedChannels(), channelGroups: this.getSubscribedChannelGroups() }, isOffline);
        }
      }, {
        key: 'getSubscribedChannels',
        value: function getSubscribedChannels() {
          return Object.keys(this._channels);
        }
      }, {
        key: 'getSubscribedChannelGroups',
        value: function getSubscribedChannelGroups() {
          return Object.keys(this._channelGroups);
        }
      }, {
        key: 'reconnect',
        value: function reconnect() {
          this._startSubscribeLoop();
          this._registerHeartbeatTimer();
        }
      }, {
        key: 'disconnect',
        value: function disconnect() {
          this._stopSubscribeLoop();
          this._stopHeartbeatTimer();
          this._reconnectionManager.stopPolling();
        }
      }, {
        key: '_registerHeartbeatTimer',
        value: function _registerHeartbeatTimer() {
          this._stopHeartbeatTimer();
          this._performHeartbeatLoop();
          this._heartbeatTimer = setInterval(this._performHeartbeatLoop.bind(this), this._config.getHeartbeatInterval() * 1000);
        }
      }, {
        key: '_stopHeartbeatTimer',
        value: function _stopHeartbeatTimer() {
          if (this._heartbeatTimer) {
            clearInterval(this._heartbeatTimer);
            this._heartbeatTimer = null;
          }
        }
      }, {
        key: '_performHeartbeatLoop',
        value: function _performHeartbeatLoop() {
          var _this4 = this;

          var presenceChannels = Object.keys(this._channels);
          var presenceChannelGroups = Object.keys(this._channelGroups);
          var presenceState = {};

          if (presenceChannels.length === 0 && presenceChannelGroups.length === 0) {
            return;
          }

          presenceChannels.forEach(function (channel) {
            var channelState = _this4._channels[channel].state;
            if (Object.keys(channelState).length) presenceState[channel] = channelState;
          });

          presenceChannelGroups.forEach(function (channelGroup) {
            var channelGroupState = _this4._channelGroups[channelGroup].state;
            if (Object.keys(channelGroupState).length) presenceState[channelGroup] = channelGroupState;
          });

          var onHeartbeat = function onHeartbeat(status) {
            if (status.error && _this4._config.announceFailedHeartbeats) {
              _this4._listenerManager.announceStatus(status);
            }

            if (!status.error && _this4._config.announceSuccessfulHeartbeats) {
              _this4._listenerManager.announceStatus(status);
            }
          };

          this._heartbeatEndpoint({
            channels: presenceChannels,
            channelGroups: presenceChannelGroups,
            state: presenceState }, onHeartbeat.bind(this));
        }
      }, {
        key: '_startSubscribeLoop',
        value: function _startSubscribeLoop() {
          this._stopSubscribeLoop();
          var channels = [];
          var channelGroups = [];

          Object.keys(this._channels).forEach(function (channel) {
            return channels.push(channel);
          });
          Object.keys(this._presenceChannels).forEach(function (channel) {
            return channels.push(channel + '-pnpres');
          });

          Object.keys(this._channelGroups).forEach(function (channelGroup) {
            return channelGroups.push(channelGroup);
          });
          Object.keys(this._presenceChannelGroups).forEach(function (channelGroup) {
            return channelGroups.push(channelGroup + '-pnpres');
          });

          if (channels.length === 0 && channelGroups.length === 0) {
            return;
          }

          var subscribeArgs = {
            channels: channels,
            channelGroups: channelGroups,
            timetoken: this._currentTimetoken,
            filterExpression: this._config.filterExpression,
            region: this._region
          };

          this._subscribeCall = this._subscribeEndpoint(subscribeArgs, this._processSubscribeResponse.bind(this));
        }
      }, {
        key: '_processSubscribeResponse',
        value: function _processSubscribeResponse(status, payload) {
          var _this5 = this;

          if (status.error) {
            if (status.category === _categories2.default.PNTimeoutCategory) {
              this._startSubscribeLoop();
            } else if (status.category === _categories2.default.PNNetworkIssuesCategory) {
              this.disconnect();
              this._reconnectionManager.onReconnection(function () {
                _this5.reconnect();
                _this5._subscriptionStatusAnnounced = true;
                var reconnectedAnnounce = {
                  category: _categories2.default.PNReconnectedCategory,
                  operation: status.operation,
                  lastTimetoken: _this5._lastTimetoken,
                  currentTimetoken: _this5._currentTimetoken
                };
                _this5._listenerManager.announceStatus(reconnectedAnnounce);
              });
              this._reconnectionManager.startPolling();
              this._listenerManager.announceStatus(status);
            } else {
              this._listenerManager.announceStatus(status);
            }

            return;
          }

          this._lastTimetoken = this._currentTimetoken;
          this._currentTimetoken = payload.metadata.timetoken;

          if (!this._subscriptionStatusAnnounced) {
            var connectedAnnounce = {};
            connectedAnnounce.category = _categories2.default.PNConnectedCategory;
            connectedAnnounce.operation = status.operation;
            connectedAnnounce.affectedChannels = this._pendingChannelSubscriptions;
            connectedAnnounce.affectedChannelGroups = this._pendingChannelGroupSubscriptions;
            connectedAnnounce.lastTimetoken = this._lastTimetoken;
            connectedAnnounce.currentTimetoken = this._currentTimetoken;
            this._subscriptionStatusAnnounced = true;
            this._listenerManager.announceStatus(connectedAnnounce);

            this._pendingChannelSubscriptions = [];
            this._pendingChannelGroupSubscriptions = [];
          }

          var messages = payload.messages || [];
          var requestMessageCountThreshold = this._config.requestMessageCountThreshold;


          if (requestMessageCountThreshold && messages.length >= requestMessageCountThreshold) {
            var countAnnouncement = {};
            countAnnouncement.category = _categories2.default.PNRequestMessageCountExceededCategory;
            countAnnouncement.operation = status.operation;
            this._listenerManager.announceStatus(countAnnouncement);
          }

          messages.forEach(function (message) {
            var channel = message.channel;
            var subscriptionMatch = message.subscriptionMatch;
            var publishMetaData = message.publishMetaData;

            if (channel === subscriptionMatch) {
              subscriptionMatch = null;
            }

            if (_utils2.default.endsWith(message.channel, '-pnpres')) {
              var announce = {};
              announce.channel = null;
              announce.subscription = null;

              announce.actualChannel = subscriptionMatch != null ? channel : null;
              announce.subscribedChannel = subscriptionMatch != null ? subscriptionMatch : channel;


              if (channel) {
                announce.channel = channel.substring(0, channel.lastIndexOf('-pnpres'));
              }

              if (subscriptionMatch) {
                announce.subscription = subscriptionMatch.substring(0, subscriptionMatch.lastIndexOf('-pnpres'));
              }

              announce.action = message.payload.action;
              announce.state = message.payload.data;
              announce.timetoken = publishMetaData.publishTimetoken;
              announce.occupancy = message.payload.occupancy;
              announce.uuid = message.payload.uuid;
              announce.timestamp = message.payload.timestamp;

              if (message.payload.join) {
                announce.join = message.payload.join;
              }

              if (message.payload.leave) {
                announce.leave = message.payload.leave;
              }

              if (message.payload.timeout) {
                announce.timeout = message.payload.timeout;
              }

              _this5._listenerManager.announcePresence(announce);
            } else {
              var _announce = {};
              _announce.channel = null;
              _announce.subscription = null;

              _announce.actualChannel = subscriptionMatch != null ? channel : null;
              _announce.subscribedChannel = subscriptionMatch != null ? subscriptionMatch : channel;


              _announce.channel = channel;
              _announce.subscription = subscriptionMatch;
              _announce.timetoken = publishMetaData.publishTimetoken;
              _announce.publisher = message.issuingClientId;

              if (_this5._config.cipherKey) {
                _announce.message = _this5._crypto.decrypt(message.payload);
              } else {
                _announce.message = message.payload;
              }

              _this5._listenerManager.announceMessage(_announce);
            }
          });

          this._region = payload.metadata.region;
          this._startSubscribeLoop();
        }
      }, {
        key: '_stopSubscribeLoop',
        value: function _stopSubscribeLoop() {
          if (this._subscribeCall) {
            this._subscribeCall.abort();
            this._subscribeCall = null;
          }
        }
      }]);

      return _class;
    }();

    exports.default = _class;
    module.exports = exports['default'];

/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });

    var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

    var _flow_interfaces = __webpack_require__(8);

    var _categories = __webpack_require__(13);

    var _categories2 = _interopRequireDefault(_categories);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

    var _class = function () {
      function _class() {
        _classCallCheck(this, _class);

        this._listeners = [];
      }

      _createClass(_class, [{
        key: 'addListener',
        value: function addListener(newListeners) {
          this._listeners.push(newListeners);
        }
      }, {
        key: 'removeListener',
        value: function removeListener(deprecatedListener) {
          var newListeners = [];

          this._listeners.forEach(function (listener) {
            if (listener !== deprecatedListener) newListeners.push(listener);
          });

          this._listeners = newListeners;
        }
      }, {
        key: 'removeAllListeners',
        value: function removeAllListeners() {
          this._listeners = [];
        }
      }, {
        key: 'announcePresence',
        value: function announcePresence(announce) {
          this._listeners.forEach(function (listener) {
            if (listener.presence) listener.presence(announce);
          });
        }
      }, {
        key: 'announceStatus',
        value: function announceStatus(announce) {
          this._listeners.forEach(function (listener) {
            if (listener.status) listener.status(announce);
          });
        }
      }, {
        key: 'announceMessage',
        value: function announceMessage(announce) {
          this._listeners.forEach(function (listener) {
            if (listener.message) listener.message(announce);
          });
        }
      }, {
        key: 'announceNetworkUp',
        value: function announceNetworkUp() {
          var networkStatus = {};
          networkStatus.category = _categories2.default.PNNetworkUpCategory;
          this.announceStatus(networkStatus);
        }
      }, {
        key: 'announceNetworkDown',
        value: function announceNetworkDown() {
          var networkStatus = {};
          networkStatus.category = _categories2.default.PNNetworkDownCategory;
          this.announceStatus(networkStatus);
        }
      }]);

      return _class;
    }();

    exports.default = _class;
    module.exports = exports['default'];

/***/ },
/* 13 */
/***/ function(module, exports) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.default = {
      PNNetworkUpCategory: 'PNNetworkUpCategory',

      PNNetworkDownCategory: 'PNNetworkDownCategory',

      PNNetworkIssuesCategory: 'PNNetworkIssuesCategory',

      PNTimeoutCategory: 'PNTimeoutCategory',

      PNBadRequestCategory: 'PNBadRequestCategory',

      PNAccessDeniedCategory: 'PNAccessDeniedCategory',

      PNUnknownCategory: 'PNUnknownCategory',

      PNReconnectedCategory: 'PNReconnectedCategory',

      PNConnectedCategory: 'PNConnectedCategory',

      PNRequestMessageCountExceededCategory: 'PNRequestMessageCountExceededCategory'

    };
    module.exports = exports['default'];

/***/ },
/* 14 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });

    var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

    var _time = __webpack_require__(15);

    var _time2 = _interopRequireDefault(_time);

    var _flow_interfaces = __webpack_require__(8);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

    var _class = function () {
      function _class(_ref) {
        var timeEndpoint = _ref.timeEndpoint;

        _classCallCheck(this, _class);

        this._timeEndpoint = timeEndpoint;
      }

      _createClass(_class, [{
        key: 'onReconnection',
        value: function onReconnection(reconnectionCallback) {
          this._reconnectionCallback = reconnectionCallback;
        }
      }, {
        key: 'startPolling',
        value: function startPolling() {
          this._timeTimer = setInterval(this._performTimeLoop.bind(this), 3000);
        }
      }, {
        key: 'stopPolling',
        value: function stopPolling() {
          clearInterval(this._timeTimer);
        }
      }, {
        key: '_performTimeLoop',
        value: function _performTimeLoop() {
          var _this = this;

          this._timeEndpoint(function (status) {
            if (!status.error) {
              clearInterval(_this._timeTimer);
              _this._reconnectionCallback();
            }
          });
        }
      }]);

      return _class;
    }();

    exports.default = _class;
    module.exports = exports['default'];

/***/ },
/* 15 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.prepareParams = prepareParams;
    exports.isAuthSupported = isAuthSupported;
    exports.handleResponse = handleResponse;
    exports.validateParams = validateParams;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNTimeOperation;
    }

    function getURL() {
      return '/time/0';
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function prepareParams() {
      return {};
    }

    function isAuthSupported() {
      return false;
    }

    function handleResponse(modules, serverResponse) {
      return {
        timetoken: serverResponse[0]
      };
    }

    function validateParams() {}

/***/ },
/* 16 */
/***/ function(module, exports) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.default = {
      PNTimeOperation: 'PNTimeOperation',

      PNHistoryOperation: 'PNHistoryOperation',
      PNFetchMessagesOperation: 'PNFetchMessagesOperation',

      PNSubscribeOperation: 'PNSubscribeOperation',
      PNUnsubscribeOperation: 'PNUnsubscribeOperation',
      PNPublishOperation: 'PNPublishOperation',

      PNPushNotificationEnabledChannelsOperation: 'PNPushNotificationEnabledChannelsOperation',
      PNRemoveAllPushNotificationsOperation: 'PNRemoveAllPushNotificationsOperation',

      PNWhereNowOperation: 'PNWhereNowOperation',
      PNSetStateOperation: 'PNSetStateOperation',
      PNHereNowOperation: 'PNHereNowOperation',
      PNGetStateOperation: 'PNGetStateOperation',
      PNHeartbeatOperation: 'PNHeartbeatOperation',

      PNChannelGroupsOperation: 'PNChannelGroupsOperation',
      PNRemoveGroupOperation: 'PNRemoveGroupOperation',
      PNChannelsForGroupOperation: 'PNChannelsForGroupOperation',
      PNAddChannelsToGroupOperation: 'PNAddChannelsToGroupOperation',
      PNRemoveChannelsFromGroupOperation: 'PNRemoveChannelsFromGroupOperation',

      PNAccessManagerGrant: 'PNAccessManagerGrant',
      PNAccessManagerAudit: 'PNAccessManagerAudit'
    };
    module.exports = exports['default'];

/***/ },
/* 17 */
/***/ function(module, exports) {

    'use strict';

    function objectToList(o) {
      var l = [];
      Object.keys(o).forEach(function (key) {
        return l.push(key);
      });
      return l;
    }

    function encodeString(input) {
      return encodeURIComponent(input).replace(/[!~*'()]/g, function (x) {
        return '%' + x.charCodeAt(0).toString(16).toUpperCase();
      });
    }

    function objectToListSorted(o) {
      return objectToList(o).sort();
    }

    function signPamFromParams(params) {
      var l = objectToListSorted(params);
      return l.map(function (paramKey) {
        return paramKey + '=' + encodeString(params[paramKey]);
      }).join('&');
    }

    function endsWith(searchString, suffix) {
      return searchString.indexOf(suffix, this.length - suffix.length) !== -1;
    }

    function createPromise() {
      var successResolve = void 0;
      var failureResolve = void 0;
      var promise = new Promise(function (fulfill, reject) {
        successResolve = fulfill;
        failureResolve = reject;
      });

      return { promise: promise, reject: failureResolve, fulfill: successResolve };
    }

    module.exports = { signPamFromParams: signPamFromParams, endsWith: endsWith, createPromise: createPromise, encodeString: encodeString };

/***/ },
/* 18 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });

    exports.default = function (modules, endpoint) {
      var networking = modules.networking,
          config = modules.config;

      var callback = null;
      var promiseComponent = null;
      var incomingParams = {};

      if (endpoint.getOperation() === _operations2.default.PNTimeOperation || endpoint.getOperation() === _operations2.default.PNChannelGroupsOperation) {
        callback = arguments.length <= 2 ? undefined : arguments[2];
      } else {
        incomingParams = arguments.length <= 2 ? undefined : arguments[2];
        callback = arguments.length <= 3 ? undefined : arguments[3];
      }

      if (typeof Promise !== 'undefined' && !callback) {
        promiseComponent = _utils2.default.createPromise();
      }

      var validationResult = endpoint.validateParams(modules, incomingParams);

      if (validationResult) {
        if (callback) {
          return callback(createValidationError(validationResult));
        } else if (promiseComponent) {
          promiseComponent.reject(new PubNubError('Validation failed, check status for details', createValidationError(validationResult)));
          return promiseComponent.promise;
        }
        return;
      }

      var outgoingParams = endpoint.prepareParams(modules, incomingParams);
      var url = decideURL(endpoint, modules, incomingParams);
      var callInstance = void 0;
      var networkingParams = { url: url,
        operation: endpoint.getOperation(),
        timeout: endpoint.getRequestTimeout(modules)
      };

      outgoingParams.uuid = config.UUID;
      outgoingParams.pnsdk = generatePNSDK(config);

      if (config.useInstanceId) {
        outgoingParams.instanceid = config.instanceId;
      }

      if (config.useRequestId) {
        outgoingParams.requestid = _uuid2.default.v4();
      }

      if (endpoint.isAuthSupported() && config.getAuthKey()) {
        outgoingParams.auth = config.getAuthKey();
      }

      if (config.secretKey) {
        signRequest(modules, url, outgoingParams);
      }

      var onResponse = function onResponse(status, payload) {
        if (status.error) {
          if (callback) {
            callback(status);
          } else if (promiseComponent) {
            promiseComponent.reject(new PubNubError('PubNub call failed, check status for details', status));
          }
          return;
        }

        var parsedPayload = endpoint.handleResponse(modules, payload, incomingParams);

        if (callback) {
          callback(status, parsedPayload);
        } else if (promiseComponent) {
          promiseComponent.fulfill(parsedPayload);
        }
      };

      if (endpoint.usePost && endpoint.usePost(modules, incomingParams)) {
        var payload = endpoint.postPayload(modules, incomingParams);
        callInstance = networking.POST(outgoingParams, payload, networkingParams, onResponse);
      } else {
        callInstance = networking.GET(outgoingParams, networkingParams, onResponse);
      }

      if (endpoint.getOperation() === _operations2.default.PNSubscribeOperation) {
        return callInstance;
      }

      if (promiseComponent) {
        return promiseComponent.promise;
      }
    };

    var _uuid = __webpack_require__(2);

    var _uuid2 = _interopRequireDefault(_uuid);

    var _flow_interfaces = __webpack_require__(8);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    var _config = __webpack_require__(7);

    var _config2 = _interopRequireDefault(_config);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

    function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

    function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

    var PubNubError = function (_Error) {
      _inherits(PubNubError, _Error);

      function PubNubError(message, status) {
        _classCallCheck(this, PubNubError);

        var _this = _possibleConstructorReturn(this, (PubNubError.__proto__ || Object.getPrototypeOf(PubNubError)).call(this, message));

        _this.name = _this.constructor.name;
        _this.status = status;
        _this.message = message;
        return _this;
      }

      return PubNubError;
    }(Error);

    function createError(errorPayload, type) {
      errorPayload.type = type;
      errorPayload.error = true;
      return errorPayload;
    }

    function createValidationError(message) {
      return createError({ message: message }, 'validationError');
    }

    function decideURL(endpoint, modules, incomingParams) {
      if (endpoint.usePost && endpoint.usePost(modules, incomingParams)) {
        return endpoint.postURL(modules, incomingParams);
      } else {
        return endpoint.getURL(modules, incomingParams);
      }
    }

    function generatePNSDK(config) {
      var base = 'PubNub-JS-' + config.sdkFamily;

      if (config.partnerId) {
        base += '-' + config.partnerId;
      }

      base += '/' + config.getVersion();

      return base;
    }

    function signRequest(modules, url, outgoingParams) {
      var config = modules.config,
          crypto = modules.crypto;


      outgoingParams.timestamp = Math.floor(new Date().getTime() / 1000);
      var signInput = config.subscribeKey + '\n' + config.publishKey + '\n' + url + '\n';
      signInput += _utils2.default.signPamFromParams(outgoingParams);

      var signature = crypto.HMACSHA256(signInput);
      signature = signature.replace(/\+/g, '-');
      signature = signature.replace(/\//g, '_');

      outgoingParams.signature = signature;
    }

    module.exports = exports['default'];

/***/ },
/* 19 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNAddChannelsToGroupOperation;
    }

    function validateParams(modules, incomingParams) {
      var channels = incomingParams.channels,
          channelGroup = incomingParams.channelGroup;
      var config = modules.config;


      if (!channelGroup) return 'Missing Channel Group';
      if (!channels || channels.length === 0) return 'Missing Channels';
      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var channelGroup = incomingParams.channelGroup;
      var config = modules.config;

      return '/v1/channel-registration/sub-key/' + config.subscribeKey + '/channel-group/' + _utils2.default.encodeString(channelGroup);
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(modules, incomingParams) {
      var _incomingParams$chann = incomingParams.channels,
          channels = _incomingParams$chann === undefined ? [] : _incomingParams$chann;


      return {
        add: channels.join(',')
      };
    }

    function handleResponse() {
      return {};
    }

/***/ },
/* 20 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNRemoveChannelsFromGroupOperation;
    }

    function validateParams(modules, incomingParams) {
      var channels = incomingParams.channels,
          channelGroup = incomingParams.channelGroup;
      var config = modules.config;


      if (!channelGroup) return 'Missing Channel Group';
      if (!channels || channels.length === 0) return 'Missing Channels';
      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var channelGroup = incomingParams.channelGroup;
      var config = modules.config;

      return '/v1/channel-registration/sub-key/' + config.subscribeKey + '/channel-group/' + _utils2.default.encodeString(channelGroup);
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(modules, incomingParams) {
      var _incomingParams$chann = incomingParams.channels,
          channels = _incomingParams$chann === undefined ? [] : _incomingParams$chann;


      return {
        remove: channels.join(',')
      };
    }

    function handleResponse() {
      return {};
    }

/***/ },
/* 21 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.isAuthSupported = isAuthSupported;
    exports.getRequestTimeout = getRequestTimeout;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNRemoveGroupOperation;
    }

    function validateParams(modules, incomingParams) {
      var channelGroup = incomingParams.channelGroup;
      var config = modules.config;


      if (!channelGroup) return 'Missing Channel Group';
      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var channelGroup = incomingParams.channelGroup;
      var config = modules.config;

      return '/v1/channel-registration/sub-key/' + config.subscribeKey + '/channel-group/' + _utils2.default.encodeString(channelGroup) + '/remove';
    }

    function isAuthSupported() {
      return true;
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function prepareParams() {
      return {};
    }

    function handleResponse() {
      return {};
    }

/***/ },
/* 22 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNChannelGroupsOperation;
    }

    function validateParams(modules) {
      var config = modules.config;


      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules) {
      var config = modules.config;

      return '/v1/channel-registration/sub-key/' + config.subscribeKey + '/channel-group';
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams() {
      return {};
    }

    function handleResponse(modules, serverResponse) {
      return {
        groups: serverResponse.payload.groups
      };
    }

/***/ },
/* 23 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNChannelsForGroupOperation;
    }

    function validateParams(modules, incomingParams) {
      var channelGroup = incomingParams.channelGroup;
      var config = modules.config;


      if (!channelGroup) return 'Missing Channel Group';
      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var channelGroup = incomingParams.channelGroup;
      var config = modules.config;

      return '/v1/channel-registration/sub-key/' + config.subscribeKey + '/channel-group/' + _utils2.default.encodeString(channelGroup);
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams() {
      return {};
    }

    function handleResponse(modules, serverResponse) {
      return {
        channels: serverResponse.payload.channels
      };
    }

/***/ },
/* 24 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNPushNotificationEnabledChannelsOperation;
    }

    function validateParams(modules, incomingParams) {
      var device = incomingParams.device,
          pushGateway = incomingParams.pushGateway,
          channels = incomingParams.channels;
      var config = modules.config;


      if (!device) return 'Missing Device ID (device)';
      if (!pushGateway) return 'Missing GW Type (pushGateway: gcm or apns)';
      if (!channels || channels.length === 0) return 'Missing Channels';
      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var device = incomingParams.device;
      var config = modules.config;

      return '/v1/push/sub-key/' + config.subscribeKey + '/devices/' + device;
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(modules, incomingParams) {
      var pushGateway = incomingParams.pushGateway,
          _incomingParams$chann = incomingParams.channels,
          channels = _incomingParams$chann === undefined ? [] : _incomingParams$chann;

      return { type: pushGateway, add: channels.join(',') };
    }

    function handleResponse() {
      return {};
    }

/***/ },
/* 25 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNPushNotificationEnabledChannelsOperation;
    }

    function validateParams(modules, incomingParams) {
      var device = incomingParams.device,
          pushGateway = incomingParams.pushGateway,
          channels = incomingParams.channels;
      var config = modules.config;


      if (!device) return 'Missing Device ID (device)';
      if (!pushGateway) return 'Missing GW Type (pushGateway: gcm or apns)';
      if (!channels || channels.length === 0) return 'Missing Channels';
      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var device = incomingParams.device;
      var config = modules.config;

      return '/v1/push/sub-key/' + config.subscribeKey + '/devices/' + device;
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(modules, incomingParams) {
      var pushGateway = incomingParams.pushGateway,
          _incomingParams$chann = incomingParams.channels,
          channels = _incomingParams$chann === undefined ? [] : _incomingParams$chann;

      return { type: pushGateway, remove: channels.join(',') };
    }

    function handleResponse() {
      return {};
    }

/***/ },
/* 26 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNPushNotificationEnabledChannelsOperation;
    }

    function validateParams(modules, incomingParams) {
      var device = incomingParams.device,
          pushGateway = incomingParams.pushGateway;
      var config = modules.config;


      if (!device) return 'Missing Device ID (device)';
      if (!pushGateway) return 'Missing GW Type (pushGateway: gcm or apns)';
      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var device = incomingParams.device;
      var config = modules.config;

      return '/v1/push/sub-key/' + config.subscribeKey + '/devices/' + device;
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(modules, incomingParams) {
      var pushGateway = incomingParams.pushGateway;

      return { type: pushGateway };
    }

    function handleResponse(modules, serverResponse) {
      return { channels: serverResponse };
    }

/***/ },
/* 27 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNRemoveAllPushNotificationsOperation;
    }

    function validateParams(modules, incomingParams) {
      var device = incomingParams.device,
          pushGateway = incomingParams.pushGateway;
      var config = modules.config;


      if (!device) return 'Missing Device ID (device)';
      if (!pushGateway) return 'Missing GW Type (pushGateway: gcm or apns)';
      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var device = incomingParams.device;
      var config = modules.config;

      return '/v1/push/sub-key/' + config.subscribeKey + '/devices/' + device + '/remove';
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(modules, incomingParams) {
      var pushGateway = incomingParams.pushGateway;

      return { type: pushGateway };
    }

    function handleResponse() {
      return {};
    }

/***/ },
/* 28 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNUnsubscribeOperation;
    }

    function validateParams(modules) {
      var config = modules.config;


      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var config = modules.config;
      var _incomingParams$chann = incomingParams.channels,
          channels = _incomingParams$chann === undefined ? [] : _incomingParams$chann;

      var stringifiedChannels = channels.length > 0 ? channels.join(',') : ',';
      return '/v2/presence/sub-key/' + config.subscribeKey + '/channel/' + _utils2.default.encodeString(stringifiedChannels) + '/leave';
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(modules, incomingParams) {
      var _incomingParams$chann2 = incomingParams.channelGroups,
          channelGroups = _incomingParams$chann2 === undefined ? [] : _incomingParams$chann2;

      var params = {};

      if (channelGroups.length > 0) {
        params['channel-group'] = channelGroups.join(',');
      }

      return params;
    }

    function handleResponse() {
      return {};
    }

/***/ },
/* 29 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNWhereNowOperation;
    }

    function validateParams(modules) {
      var config = modules.config;


      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var config = modules.config;
      var _incomingParams$uuid = incomingParams.uuid,
          uuid = _incomingParams$uuid === undefined ? config.UUID : _incomingParams$uuid;

      return '/v2/presence/sub-key/' + config.subscribeKey + '/uuid/' + uuid;
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams() {
      return {};
    }

    function handleResponse(modules, serverResponse) {
      return { channels: serverResponse.payload.channels };
    }

/***/ },
/* 30 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.isAuthSupported = isAuthSupported;
    exports.getRequestTimeout = getRequestTimeout;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNHeartbeatOperation;
    }

    function validateParams(modules) {
      var config = modules.config;


      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var config = modules.config;
      var _incomingParams$chann = incomingParams.channels,
          channels = _incomingParams$chann === undefined ? [] : _incomingParams$chann;

      var stringifiedChannels = channels.length > 0 ? channels.join(',') : ',';
      return '/v2/presence/sub-key/' + config.subscribeKey + '/channel/' + _utils2.default.encodeString(stringifiedChannels) + '/heartbeat';
    }

    function isAuthSupported() {
      return true;
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function prepareParams(modules, incomingParams) {
      var _incomingParams$chann2 = incomingParams.channelGroups,
          channelGroups = _incomingParams$chann2 === undefined ? [] : _incomingParams$chann2,
          _incomingParams$state = incomingParams.state,
          state = _incomingParams$state === undefined ? {} : _incomingParams$state;
      var config = modules.config;

      var params = {};

      if (channelGroups.length > 0) {
        params['channel-group'] = channelGroups.join(',');
      }

      params.state = JSON.stringify(state);
      params.heartbeat = config.getPresenceTimeout();
      return params;
    }

    function handleResponse() {
      return {};
    }

/***/ },
/* 31 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNGetStateOperation;
    }

    function validateParams(modules) {
      var config = modules.config;


      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var config = modules.config;
      var _incomingParams$uuid = incomingParams.uuid,
          uuid = _incomingParams$uuid === undefined ? config.UUID : _incomingParams$uuid,
          _incomingParams$chann = incomingParams.channels,
          channels = _incomingParams$chann === undefined ? [] : _incomingParams$chann;

      var stringifiedChannels = channels.length > 0 ? channels.join(',') : ',';
      return '/v2/presence/sub-key/' + config.subscribeKey + '/channel/' + _utils2.default.encodeString(stringifiedChannels) + '/uuid/' + uuid;
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(modules, incomingParams) {
      var _incomingParams$chann2 = incomingParams.channelGroups,
          channelGroups = _incomingParams$chann2 === undefined ? [] : _incomingParams$chann2;

      var params = {};

      if (channelGroups.length > 0) {
        params['channel-group'] = channelGroups.join(',');
      }

      return params;
    }

    function handleResponse(modules, serverResponse, incomingParams) {
      var _incomingParams$chann3 = incomingParams.channels,
          channels = _incomingParams$chann3 === undefined ? [] : _incomingParams$chann3,
          _incomingParams$chann4 = incomingParams.channelGroups,
          channelGroups = _incomingParams$chann4 === undefined ? [] : _incomingParams$chann4;

      var channelsResponse = {};

      if (channels.length === 1 && channelGroups.length === 0) {
        channelsResponse[channels[0]] = serverResponse.payload;
      } else {
        channelsResponse = serverResponse.payload;
      }

      return { channels: channelsResponse };
    }

/***/ },
/* 32 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNSetStateOperation;
    }

    function validateParams(modules, incomingParams) {
      var config = modules.config;
      var state = incomingParams.state,
          _incomingParams$chann = incomingParams.channels,
          channels = _incomingParams$chann === undefined ? [] : _incomingParams$chann,
          _incomingParams$chann2 = incomingParams.channelGroups,
          channelGroups = _incomingParams$chann2 === undefined ? [] : _incomingParams$chann2;


      if (!state) return 'Missing State';
      if (!config.subscribeKey) return 'Missing Subscribe Key';
      if (channels.length === 0 && channelGroups.length === 0) return 'Please provide a list of channels and/or channel-groups';
    }

    function getURL(modules, incomingParams) {
      var config = modules.config;
      var _incomingParams$chann3 = incomingParams.channels,
          channels = _incomingParams$chann3 === undefined ? [] : _incomingParams$chann3;

      var stringifiedChannels = channels.length > 0 ? channels.join(',') : ',';
      return '/v2/presence/sub-key/' + config.subscribeKey + '/channel/' + _utils2.default.encodeString(stringifiedChannels) + '/uuid/' + config.UUID + '/data';
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(modules, incomingParams) {
      var state = incomingParams.state,
          _incomingParams$chann4 = incomingParams.channelGroups,
          channelGroups = _incomingParams$chann4 === undefined ? [] : _incomingParams$chann4;

      var params = {};

      params.state = JSON.stringify(state);

      if (channelGroups.length > 0) {
        params['channel-group'] = channelGroups.join(',');
      }

      return params;
    }

    function handleResponse(modules, serverResponse) {
      return { state: serverResponse.payload };
    }

/***/ },
/* 33 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNHereNowOperation;
    }

    function validateParams(modules) {
      var config = modules.config;


      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var config = modules.config;
      var _incomingParams$chann = incomingParams.channels,
          channels = _incomingParams$chann === undefined ? [] : _incomingParams$chann,
          _incomingParams$chann2 = incomingParams.channelGroups,
          channelGroups = _incomingParams$chann2 === undefined ? [] : _incomingParams$chann2;

      var baseURL = '/v2/presence/sub-key/' + config.subscribeKey;

      if (channels.length > 0 || channelGroups.length > 0) {
        var stringifiedChannels = channels.length > 0 ? channels.join(',') : ',';
        baseURL += '/channel/' + _utils2.default.encodeString(stringifiedChannels);
      }

      return baseURL;
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(modules, incomingParams) {
      var _incomingParams$chann3 = incomingParams.channelGroups,
          channelGroups = _incomingParams$chann3 === undefined ? [] : _incomingParams$chann3,
          _incomingParams$inclu = incomingParams.includeUUIDs,
          includeUUIDs = _incomingParams$inclu === undefined ? true : _incomingParams$inclu,
          _incomingParams$inclu2 = incomingParams.includeState,
          includeState = _incomingParams$inclu2 === undefined ? false : _incomingParams$inclu2;

      var params = {};

      if (!includeUUIDs) params.disable_uuids = 1;
      if (includeState) params.state = 1;

      if (channelGroups.length > 0) {
        params['channel-group'] = channelGroups.join(',');
      }

      return params;
    }

    function handleResponse(modules, serverResponse, incomingParams) {
      var _incomingParams$chann4 = incomingParams.channels,
          channels = _incomingParams$chann4 === undefined ? [] : _incomingParams$chann4,
          _incomingParams$chann5 = incomingParams.channelGroups,
          channelGroups = _incomingParams$chann5 === undefined ? [] : _incomingParams$chann5,
          _incomingParams$inclu3 = incomingParams.includeUUIDs,
          includeUUIDs = _incomingParams$inclu3 === undefined ? true : _incomingParams$inclu3,
          _incomingParams$inclu4 = incomingParams.includeState,
          includeState = _incomingParams$inclu4 === undefined ? false : _incomingParams$inclu4;


      var prepareSingularChannel = function prepareSingularChannel() {
        var response = {};
        var occupantsList = [];
        response.totalChannels = 1;
        response.totalOccupancy = serverResponse.occupancy;
        response.channels = {};
        response.channels[channels[0]] = {
          occupants: occupantsList,
          name: channels[0],
          occupancy: serverResponse.occupancy
        };

        if (includeUUIDs) {
          serverResponse.uuids.forEach(function (uuidEntry) {
            if (includeState) {
              occupantsList.push({ state: uuidEntry.state, uuid: uuidEntry.uuid });
            } else {
              occupantsList.push({ state: null, uuid: uuidEntry });
            }
          });
        }

        return response;
      };

      var prepareMultipleChannel = function prepareMultipleChannel() {
        var response = {};
        response.totalChannels = serverResponse.payload.total_channels;
        response.totalOccupancy = serverResponse.payload.total_occupancy;
        response.channels = {};

        Object.keys(serverResponse.payload.channels).forEach(function (channelName) {
          var channelEntry = serverResponse.payload.channels[channelName];
          var occupantsList = [];
          response.channels[channelName] = {
            occupants: occupantsList,
            name: channelName,
            occupancy: channelEntry.occupancy
          };

          if (includeUUIDs) {
            channelEntry.uuids.forEach(function (uuidEntry) {
              if (includeState) {
                occupantsList.push({ state: uuidEntry.state, uuid: uuidEntry.uuid });
              } else {
                occupantsList.push({ state: null, uuid: uuidEntry });
              }
            });
          }

          return response;
        });

        return response;
      };

      var response = void 0;
      if (channels.length > 1 || channelGroups.length > 0 || channelGroups.length === 0 && channels.length === 0) {
        response = prepareMultipleChannel();
      } else {
        response = prepareSingularChannel();
      }

      return response;
    }

/***/ },
/* 34 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNAccessManagerAudit;
    }

    function validateParams(modules) {
      var config = modules.config;


      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules) {
      var config = modules.config;

      return '/v2/auth/audit/sub-key/' + config.subscribeKey;
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return false;
    }

    function prepareParams(modules, incomingParams) {
      var channel = incomingParams.channel,
          channelGroup = incomingParams.channelGroup,
          _incomingParams$authK = incomingParams.authKeys,
          authKeys = _incomingParams$authK === undefined ? [] : _incomingParams$authK;

      var params = {};

      if (channel) {
        params.channel = channel;
      }

      if (channelGroup) {
        params['channel-group'] = channelGroup;
      }

      if (authKeys.length > 0) {
        params.auth = authKeys.join(',');
      }

      return params;
    }

    function handleResponse(modules, serverResponse) {
      return serverResponse.payload;
    }

/***/ },
/* 35 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNAccessManagerGrant;
    }

    function validateParams(modules) {
      var config = modules.config;


      if (!config.subscribeKey) return 'Missing Subscribe Key';
      if (!config.publishKey) return 'Missing Publish Key';
      if (!config.secretKey) return 'Missing Secret Key';
    }

    function getURL(modules) {
      var config = modules.config;

      return '/v2/auth/grant/sub-key/' + config.subscribeKey;
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return false;
    }

    function prepareParams(modules, incomingParams) {
      var _incomingParams$chann = incomingParams.channels,
          channels = _incomingParams$chann === undefined ? [] : _incomingParams$chann,
          _incomingParams$chann2 = incomingParams.channelGroups,
          channelGroups = _incomingParams$chann2 === undefined ? [] : _incomingParams$chann2,
          ttl = incomingParams.ttl,
          _incomingParams$read = incomingParams.read,
          read = _incomingParams$read === undefined ? false : _incomingParams$read,
          _incomingParams$write = incomingParams.write,
          write = _incomingParams$write === undefined ? false : _incomingParams$write,
          _incomingParams$manag = incomingParams.manage,
          manage = _incomingParams$manag === undefined ? false : _incomingParams$manag,
          _incomingParams$authK = incomingParams.authKeys,
          authKeys = _incomingParams$authK === undefined ? [] : _incomingParams$authK;

      var params = {};

      params.r = read ? '1' : '0';
      params.w = write ? '1' : '0';
      params.m = manage ? '1' : '0';

      if (channels.length > 0) {
        params.channel = channels.join(',');
      }

      if (channelGroups.length > 0) {
        params['channel-group'] = channelGroups.join(',');
      }

      if (authKeys.length > 0) {
        params.auth = authKeys.join(',');
      }

      if (ttl || ttl === 0) {
        params.ttl = ttl;
      }

      return params;
    }

    function handleResponse() {
      return {};
    }

/***/ },
/* 36 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });

    var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.usePost = usePost;
    exports.getURL = getURL;
    exports.postURL = postURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.postPayload = postPayload;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function prepareMessagePayload(modules, messagePayload) {
      var crypto = modules.crypto,
          config = modules.config;

      var stringifiedPayload = JSON.stringify(messagePayload);

      if (config.cipherKey) {
        stringifiedPayload = crypto.encrypt(stringifiedPayload);
        stringifiedPayload = JSON.stringify(stringifiedPayload);
      }

      return stringifiedPayload;
    }

    function getOperation() {
      return _operations2.default.PNPublishOperation;
    }

    function validateParams(_ref, incomingParams) {
      var config = _ref.config;
      var message = incomingParams.message,
          channel = incomingParams.channel;


      if (!channel) return 'Missing Channel';
      if (!message) return 'Missing Message';
      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function usePost(modules, incomingParams) {
      var _incomingParams$sendB = incomingParams.sendByPost,
          sendByPost = _incomingParams$sendB === undefined ? false : _incomingParams$sendB;

      return sendByPost;
    }

    function getURL(modules, incomingParams) {
      var config = modules.config;
      var channel = incomingParams.channel,
          message = incomingParams.message;

      var stringifiedPayload = prepareMessagePayload(modules, message);
      return '/publish/' + config.publishKey + '/' + config.subscribeKey + '/0/' + _utils2.default.encodeString(channel) + '/0/' + _utils2.default.encodeString(stringifiedPayload);
    }

    function postURL(modules, incomingParams) {
      var config = modules.config;
      var channel = incomingParams.channel;

      return '/publish/' + config.publishKey + '/' + config.subscribeKey + '/0/' + _utils2.default.encodeString(channel) + '/0';
    }

    function getRequestTimeout(_ref2) {
      var config = _ref2.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function postPayload(modules, incomingParams) {
      var message = incomingParams.message;

      return prepareMessagePayload(modules, message);
    }

    function prepareParams(modules, incomingParams) {
      var meta = incomingParams.meta,
          _incomingParams$repli = incomingParams.replicate,
          replicate = _incomingParams$repli === undefined ? true : _incomingParams$repli,
          storeInHistory = incomingParams.storeInHistory,
          ttl = incomingParams.ttl;

      var params = {};

      if (storeInHistory != null) {
        if (storeInHistory) {
          params.store = '1';
        } else {
          params.store = '0';
        }
      }

      if (ttl) {
        params.ttl = ttl;
      }

      if (replicate === false) {
        params.norep = 'true';
      }

      if (meta && (typeof meta === 'undefined' ? 'undefined' : _typeof(meta)) === 'object') {
        params.meta = JSON.stringify(meta);
      }

      return params;
    }

    function handleResponse(modules, serverResponse) {
      return { timetoken: serverResponse[2] };
    }

/***/ },
/* 37 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function __processMessage(modules, message) {
      var config = modules.config,
          crypto = modules.crypto;

      if (!config.cipherKey) return message;

      try {
        return crypto.decrypt(message);
      } catch (e) {
        return message;
      }
    }

    function getOperation() {
      return _operations2.default.PNHistoryOperation;
    }

    function validateParams(modules, incomingParams) {
      var channel = incomingParams.channel;
      var config = modules.config;


      if (!channel) return 'Missing channel';
      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var channel = incomingParams.channel;
      var config = modules.config;

      return '/v2/history/sub-key/' + config.subscribeKey + '/channel/' + _utils2.default.encodeString(channel);
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(modules, incomingParams) {
      var start = incomingParams.start,
          end = incomingParams.end,
          reverse = incomingParams.reverse,
          _incomingParams$count = incomingParams.count,
          count = _incomingParams$count === undefined ? 100 : _incomingParams$count,
          _incomingParams$strin = incomingParams.stringifiedTimeToken,
          stringifiedTimeToken = _incomingParams$strin === undefined ? false : _incomingParams$strin;

      var outgoingParams = {
        include_token: 'true'
      };

      outgoingParams.count = count;
      if (start) outgoingParams.start = start;
      if (end) outgoingParams.end = end;
      if (stringifiedTimeToken) outgoingParams.string_message_token = 'true';
      if (reverse != null) outgoingParams.reverse = reverse.toString();

      return outgoingParams;
    }

    function handleResponse(modules, serverResponse) {
      var response = {
        messages: [],
        startTimeToken: serverResponse[1],
        endTimeToken: serverResponse[2]
      };

      serverResponse[0].forEach(function (serverHistoryItem) {
        var item = {
          timetoken: serverHistoryItem.timetoken,
          entry: __processMessage(modules, serverHistoryItem.message)
        };

        response.messages.push(item);
      });

      return response;
    }

/***/ },
/* 38 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function __processMessage(modules, message) {
      var config = modules.config,
          crypto = modules.crypto;

      if (!config.cipherKey) return message;

      try {
        return crypto.decrypt(message);
      } catch (e) {
        return message;
      }
    }

    function getOperation() {
      return _operations2.default.PNFetchMessagesOperation;
    }

    function validateParams(modules, incomingParams) {
      var channels = incomingParams.channels;
      var config = modules.config;


      if (!channels || channels.length === 0) return 'Missing channels';
      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var _incomingParams$chann = incomingParams.channels,
          channels = _incomingParams$chann === undefined ? [] : _incomingParams$chann;
      var config = modules.config;


      var stringifiedChannels = channels.length > 0 ? channels.join(',') : ',';
      return '/v3/history/sub-key/' + config.subscribeKey + '/channel/' + _utils2.default.encodeString(stringifiedChannels);
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getTransactionTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(modules, incomingParams) {
      var start = incomingParams.start,
          end = incomingParams.end,
          count = incomingParams.count;

      var outgoingParams = {};

      if (count) outgoingParams.max = count;
      if (start) outgoingParams.start = start;
      if (end) outgoingParams.end = end;

      return outgoingParams;
    }

    function handleResponse(modules, serverResponse) {
      var response = {
        channels: {}
      };

      Object.keys(serverResponse.channels || {}).forEach(function (channelName) {
        response.channels[channelName] = [];

        (serverResponse.channels[channelName] || []).forEach(function (messageEnvelope) {
          var announce = {};
          announce.channel = channelName;
          announce.subscription = null;
          announce.timetoken = messageEnvelope.timetoken;
          announce.message = __processMessage(modules, messageEnvelope.message);
          response.channels[channelName].push(announce);
        });
      });

      return response;
    }

/***/ },
/* 39 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.getOperation = getOperation;
    exports.validateParams = validateParams;
    exports.getURL = getURL;
    exports.getRequestTimeout = getRequestTimeout;
    exports.isAuthSupported = isAuthSupported;
    exports.prepareParams = prepareParams;
    exports.handleResponse = handleResponse;

    var _flow_interfaces = __webpack_require__(8);

    var _operations = __webpack_require__(16);

    var _operations2 = _interopRequireDefault(_operations);

    var _utils = __webpack_require__(17);

    var _utils2 = _interopRequireDefault(_utils);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function getOperation() {
      return _operations2.default.PNSubscribeOperation;
    }

    function validateParams(modules) {
      var config = modules.config;


      if (!config.subscribeKey) return 'Missing Subscribe Key';
    }

    function getURL(modules, incomingParams) {
      var config = modules.config;
      var _incomingParams$chann = incomingParams.channels,
          channels = _incomingParams$chann === undefined ? [] : _incomingParams$chann;

      var stringifiedChannels = channels.length > 0 ? channels.join(',') : ',';
      return '/v2/subscribe/' + config.subscribeKey + '/' + _utils2.default.encodeString(stringifiedChannels) + '/0';
    }

    function getRequestTimeout(_ref) {
      var config = _ref.config;

      return config.getSubscribeTimeout();
    }

    function isAuthSupported() {
      return true;
    }

    function prepareParams(_ref2, incomingParams) {
      var config = _ref2.config;
      var _incomingParams$chann2 = incomingParams.channelGroups,
          channelGroups = _incomingParams$chann2 === undefined ? [] : _incomingParams$chann2,
          timetoken = incomingParams.timetoken,
          filterExpression = incomingParams.filterExpression,
          region = incomingParams.region;

      var params = {
        heartbeat: config.getPresenceTimeout()
      };

      if (channelGroups.length > 0) {
        params['channel-group'] = channelGroups.join(',');
      }

      if (filterExpression && filterExpression.length > 0) {
        params['filter-expr'] = filterExpression;
      }

      if (timetoken) {
        params.tt = timetoken;
      }

      if (region) {
        params.tr = region;
      }

      return params;
    }

    function handleResponse(modules, serverResponse) {
      var messages = [];

      serverResponse.m.forEach(function (rawMessage) {
        var publishMetaData = {
          publishTimetoken: rawMessage.p.t,
          region: rawMessage.p.r
        };
        var parsedMessage = {
          shard: parseInt(rawMessage.a, 10),
          subscriptionMatch: rawMessage.b,
          channel: rawMessage.c,
          payload: rawMessage.d,
          flags: rawMessage.f,
          issuingClientId: rawMessage.i,
          subscribeKey: rawMessage.k,
          originationTimetoken: rawMessage.o,
          publishMetaData: publishMetaData
        };
        messages.push(parsedMessage);
      });

      var metadata = {
        timetoken: serverResponse.t.t,
        region: serverResponse.t.r
      };

      return { messages: messages, metadata: metadata };
    }

/***/ },
/* 40 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });

    var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

    var _config = __webpack_require__(7);

    var _config2 = _interopRequireDefault(_config);

    var _categories = __webpack_require__(13);

    var _categories2 = _interopRequireDefault(_categories);

    var _flow_interfaces = __webpack_require__(8);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

    var _class = function () {
      function _class(modules) {
        var _this = this;

        _classCallCheck(this, _class);

        this._modules = {};

        Object.keys(modules).forEach(function (key) {
          _this._modules[key] = modules[key].bind(_this);
        });
      }

      _createClass(_class, [{
        key: 'init',
        value: function init(config) {
          this._config = config;

          this._maxSubDomain = 20;
          this._currentSubDomain = Math.floor(Math.random() * this._maxSubDomain);
          this._providedFQDN = (this._config.secure ? 'https://' : 'http://') + this._config.origin;
          this._coreParams = {};

          this.shiftStandardOrigin();
        }
      }, {
        key: 'nextOrigin',
        value: function nextOrigin() {
          if (this._providedFQDN.indexOf('pubsub.') === -1) {
            return this._providedFQDN;
          }

          var newSubDomain = void 0;

          this._currentSubDomain = this._currentSubDomain + 1;

          if (this._currentSubDomain >= this._maxSubDomain) {
            this._currentSubDomain = 1;
          }

          newSubDomain = this._currentSubDomain.toString();

          return this._providedFQDN.replace('pubsub', 'ps' + newSubDomain);
        }
      }, {
        key: 'shiftStandardOrigin',
        value: function shiftStandardOrigin() {
          var failover = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

          this._standardOrigin = this.nextOrigin(failover);

          return this._standardOrigin;
        }
      }, {
        key: 'getStandardOrigin',
        value: function getStandardOrigin() {
          return this._standardOrigin;
        }
      }, {
        key: 'POST',
        value: function POST(params, body, endpoint, callback) {
          return this._modules.post(params, body, endpoint, callback);
        }
      }, {
        key: 'GET',
        value: function GET(params, endpoint, callback) {
          return this._modules.get(params, endpoint, callback);
        }
      }, {
        key: '_detectErrorCategory',
        value: function _detectErrorCategory(err) {
          if (err.code === 'ENOTFOUND') return _categories2.default.PNNetworkIssuesCategory;
          if (err.code === 'ECONNREFUSED') return _categories2.default.PNNetworkIssuesCategory;
          if (err.code === 'ECONNRESET') return _categories2.default.PNNetworkIssuesCategory;
          if (err.code === 'EAI_AGAIN') return _categories2.default.PNNetworkIssuesCategory;

          if (err.status === 0 || err.hasOwnProperty('status') && typeof err.status === 'undefined') return _categories2.default.PNNetworkIssuesCategory;
          if (err.timeout) return _categories2.default.PNTimeoutCategory;

          if (err.response) {
            if (err.response.badRequest) return _categories2.default.PNBadRequestCategory;
            if (err.response.forbidden) return _categories2.default.PNAccessDeniedCategory;
          }

          return _categories2.default.PNUnknownCategory;
        }
      }]);

      return _class;
    }();

    exports.default = _class;
    module.exports = exports['default'];

/***/ },
/* 41 */
/***/ function(module, exports) {

    "use strict";

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.default = {
      get: function get(key) {
        try {
          return localStorage.getItem(key);
        } catch (e) {
          return null;
        }
      },
      set: function set(key, data) {
        try {
          return localStorage.setItem(key, data);
        } catch (e) {
          return null;
        }
      }
    };
    module.exports = exports["default"];

/***/ },
/* 42 */
/***/ function(module, exports, __webpack_require__) {

    'use strict';

    Object.defineProperty(exports, "__esModule", {
      value: true
    });
    exports.get = get;
    exports.post = post;

    var _superagent = __webpack_require__(43);

    var _superagent2 = _interopRequireDefault(_superagent);

    var _flow_interfaces = __webpack_require__(8);

    function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

    function log(req) {
      var _pickLogger = function _pickLogger() {
        if (console && console.log) return console;
        if (window && window.console && window.console.log) return window.console;
        return console;
      };

      var start = new Date().getTime();
      var timestamp = new Date().toISOString();
      var logger = _pickLogger();
      logger.log('<<<<<');
      logger.log('[' + timestamp + ']', '\n', req.url, '\n', req.qs);
      logger.log('-----');

      req.on('response', function (res) {
        var now = new Date().getTime();
        var elapsed = now - start;
        var timestampDone = new Date().toISOString();

        logger.log('>>>>>>');
        logger.log('[' + timestampDone + ' / ' + elapsed + ']', '\n', req.url, '\n', req.qs, '\n', res.text);
        logger.log('-----');
      });
    }

    function xdr(superagentConstruct, endpoint, callback) {
      var _this = this;

      if (this._config.logVerbosity) {
        superagentConstruct = superagentConstruct.use(log);
      }

      if (this._config.proxy && this._modules.proxy) {
        superagentConstruct = this._modules.proxy.call(this, superagentConstruct);
      }

      if (this._config.keepAlive && this._modules.keepAlive) {
        superagentConstruct = this._module.keepAlive(superagentConstruct);
      }

      return superagentConstruct.timeout(endpoint.timeout).end(function (err, resp) {
        var status = {};
        status.error = err !== null;
        status.operation = endpoint.operation;

        if (resp && resp.status) {
          status.statusCode = resp.status;
        }

        if (err) {
          status.errorData = err;
          status.category = _this._detectErrorCategory(err);
          return callback(status, null);
        }

        var parsedResponse = JSON.parse(resp.text);
        return callback(status, parsedResponse);
      });
    }

    function get(params, endpoint, callback) {
      var superagentConstruct = _superagent2.default.get(this.getStandardOrigin() + endpoint.url).query(params);
      return xdr.call(this, superagentConstruct, endpoint, callback);
    }

    function post(params, body, endpoint, callback) {
      var superagentConstruct = _superagent2.default.post(this.getStandardOrigin() + endpoint.url).query(params).send(body);
      return xdr.call(this, superagentConstruct, endpoint, callback);
    }

/***/ },
/* 43 */
/***/ function(module, exports, __webpack_require__) {

    /**
     * Root reference for iframes.
     */

    var root;
    if (typeof window !== 'undefined') { // Browser window
      root = window;
    } else if (typeof self !== 'undefined') { // Web Worker
      root = self;
    } else { // Other environments
      console.warn("Using browser-only version of superagent in non-browser environment");
      root = this;
    }

    var Emitter = __webpack_require__(44);
    var requestBase = __webpack_require__(45);
    var isObject = __webpack_require__(46);

    /**
     * Noop.
     */

    function noop(){};

    /**
     * Expose `request`.
     */

    var request = module.exports = __webpack_require__(47).bind(null, Request);

    /**
     * Determine XHR.
     */

    request.getXHR = function () {
      if (root.XMLHttpRequest
          && (!root.location || 'file:' != root.location.protocol
              || !root.ActiveXObject)) {
        return new XMLHttpRequest;
      } else {
        try { return new ActiveXObject('Microsoft.XMLHTTP'); } catch(e) {}
        try { return new ActiveXObject('Msxml2.XMLHTTP.6.0'); } catch(e) {}
        try { return new ActiveXObject('Msxml2.XMLHTTP.3.0'); } catch(e) {}
        try { return new ActiveXObject('Msxml2.XMLHTTP'); } catch(e) {}
      }
      throw Error("Browser-only verison of superagent could not find XHR");
    };

    /**
     * Removes leading and trailing whitespace, added to support IE.
     *
     * @param {String} s
     * @return {String}
     * @api private
     */

    var trim = ''.trim
      ? function(s) { return s.trim(); }
      : function(s) { return s.replace(/(^\s*|\s*$)/g, ''); };

    /**
     * Serialize the given `obj`.
     *
     * @param {Object} obj
     * @return {String}
     * @api private
     */

    function serialize(obj) {
      if (!isObject(obj)) return obj;
      var pairs = [];
      for (var key in obj) {
        pushEncodedKeyValuePair(pairs, key, obj[key]);
      }
      return pairs.join('&');
    }

    /**
     * Helps 'serialize' with serializing arrays.
     * Mutates the pairs array.
     *
     * @param {Array} pairs
     * @param {String} key
     * @param {Mixed} val
     */

    function pushEncodedKeyValuePair(pairs, key, val) {
      if (val != null) {
        if (Array.isArray(val)) {
          val.forEach(function(v) {
            pushEncodedKeyValuePair(pairs, key, v);
          });
        } else if (isObject(val)) {
          for(var subkey in val) {
            pushEncodedKeyValuePair(pairs, key + '[' + subkey + ']', val[subkey]);
          }
        } else {
          pairs.push(encodeURIComponent(key)
            + '=' + encodeURIComponent(val));
        }
      } else if (val === null) {
        pairs.push(encodeURIComponent(key));
      }
    }

    /**
     * Expose serialization method.
     */

     request.serializeObject = serialize;

     /**
      * Parse the given x-www-form-urlencoded `str`.
      *
      * @param {String} str
      * @return {Object}
      * @api private
      */

    function parseString(str) {
      var obj = {};
      var pairs = str.split('&');
      var pair;
      var pos;

      for (var i = 0, len = pairs.length; i < len; ++i) {
        pair = pairs[i];
        pos = pair.indexOf('=');
        if (pos == -1) {
          obj[decodeURIComponent(pair)] = '';
        } else {
          obj[decodeURIComponent(pair.slice(0, pos))] =
            decodeURIComponent(pair.slice(pos + 1));
        }
      }

      return obj;
    }

    /**
     * Expose parser.
     */

    request.parseString = parseString;

    /**
     * Default MIME type map.
     *
     *     superagent.types.xml = 'application/xml';
     *
     */

    request.types = {
      html: 'text/html',
      json: 'application/json',
      xml: 'application/xml',
      urlencoded: 'application/x-www-form-urlencoded',
      'form': 'application/x-www-form-urlencoded',
      'form-data': 'application/x-www-form-urlencoded'
    };

    /**
     * Default serialization map.
     *
     *     superagent.serialize['application/xml'] = function(obj){
     *       return 'generated xml here';
     *     };
     *
     */

     request.serialize = {
       'application/x-www-form-urlencoded': serialize,
       'application/json': JSON.stringify
     };

     /**
      * Default parsers.
      *
      *     superagent.parse['application/xml'] = function(str){
      *       return { object parsed from str };
      *     };
      *
      */

    request.parse = {
      'application/x-www-form-urlencoded': parseString,
      'application/json': JSON.parse
    };

    /**
     * Parse the given header `str` into
     * an object containing the mapped fields.
     *
     * @param {String} str
     * @return {Object}
     * @api private
     */

    function parseHeader(str) {
      var lines = str.split(/\r?\n/);
      var fields = {};
      var index;
      var line;
      var field;
      var val;

      lines.pop(); // trailing CRLF

      for (var i = 0, len = lines.length; i < len; ++i) {
        line = lines[i];
        index = line.indexOf(':');
        field = line.slice(0, index).toLowerCase();
        val = trim(line.slice(index + 1));
        fields[field] = val;
      }

      return fields;
    }

    /**
     * Check if `mime` is json or has +json structured syntax suffix.
     *
     * @param {String} mime
     * @return {Boolean}
     * @api private
     */

    function isJSON(mime) {
      return /[\/+]json\b/.test(mime);
    }

    /**
     * Return the mime type for the given `str`.
     *
     * @param {String} str
     * @return {String}
     * @api private
     */

    function type(str){
      return str.split(/ *; */).shift();
    };

    /**
     * Return header field parameters.
     *
     * @param {String} str
     * @return {Object}
     * @api private
     */

    function params(str){
      return str.split(/ *; */).reduce(function(obj, str){
        var parts = str.split(/ *= */),
            key = parts.shift(),
            val = parts.shift();

        if (key && val) obj[key] = val;
        return obj;
      }, {});
    };

    /**
     * Initialize a new `Response` with the given `xhr`.
     *
     *  - set flags (.ok, .error, etc)
     *  - parse header
     *
     * Examples:
     *
     *  Aliasing `superagent` as `request` is nice:
     *
     *      request = superagent;
     *
     *  We can use the promise-like API, or pass callbacks:
     *
     *      request.get('/').end(function(res){});
     *      request.get('/', function(res){});
     *
     *  Sending data can be chained:
     *
     *      request
     *        .post('/user')
     *        .send({ name: 'tj' })
     *        .end(function(res){});
     *
     *  Or passed to `.send()`:
     *
     *      request
     *        .post('/user')
     *        .send({ name: 'tj' }, function(res){});
     *
     *  Or passed to `.post()`:
     *
     *      request
     *        .post('/user', { name: 'tj' })
     *        .end(function(res){});
     *
     * Or further reduced to a single call for simple cases:
     *
     *      request
     *        .post('/user', { name: 'tj' }, function(res){});
     *
     * @param {XMLHTTPRequest} xhr
     * @param {Object} options
     * @api private
     */

    function Response(req, options) {
      options = options || {};
      this.req = req;
      this.xhr = this.req.xhr;
      // responseText is accessible only if responseType is '' or 'text' and on older browsers
      this.text = ((this.req.method !='HEAD' && (this.xhr.responseType === '' || this.xhr.responseType === 'text')) || typeof this.xhr.responseType === 'undefined')
         ? this.xhr.responseText
         : null;
      this.statusText = this.req.xhr.statusText;
      this._setStatusProperties(this.xhr.status);
      this.header = this.headers = parseHeader(this.xhr.getAllResponseHeaders());
      // getAllResponseHeaders sometimes falsely returns "" for CORS requests, but
      // getResponseHeader still works. so we get content-type even if getting
      // other headers fails.
      this.header['content-type'] = this.xhr.getResponseHeader('content-type');
      this._setHeaderProperties(this.header);
      this.body = this.req.method != 'HEAD'
        ? this._parseBody(this.text ? this.text : this.xhr.response)
        : null;
    }

    /**
     * Get case-insensitive `field` value.
     *
     * @param {String} field
     * @return {String}
     * @api public
     */

    Response.prototype.get = function(field){
      return this.header[field.toLowerCase()];
    };

    /**
     * Set header related properties:
     *
     *   - `.type` the content type without params
     *
     * A response of "Content-Type: text/plain; charset=utf-8"
     * will provide you with a `.type` of "text/plain".
     *
     * @param {Object} header
     * @api private
     */

    Response.prototype._setHeaderProperties = function(header){
      // content-type
      var ct = this.header['content-type'] || '';
      this.type = type(ct);

      // params
      var obj = params(ct);
      for (var key in obj) this[key] = obj[key];
    };

    /**
     * Parse the given body `str`.
     *
     * Used for auto-parsing of bodies. Parsers
     * are defined on the `superagent.parse` object.
     *
     * @param {String} str
     * @return {Mixed}
     * @api private
     */

    Response.prototype._parseBody = function(str){
      var parse = request.parse[this.type];
      if (!parse && isJSON(this.type)) {
        parse = request.parse['application/json'];
      }
      return parse && str && (str.length || str instanceof Object)
        ? parse(str)
        : null;
    };

    /**
     * Set flags such as `.ok` based on `status`.
     *
     * For example a 2xx response will give you a `.ok` of __true__
     * whereas 5xx will be __false__ and `.error` will be __true__. The
     * `.clientError` and `.serverError` are also available to be more
     * specific, and `.statusType` is the class of error ranging from 1..5
     * sometimes useful for mapping respond colors etc.
     *
     * "sugar" properties are also defined for common cases. Currently providing:
     *
     *   - .noContent
     *   - .badRequest
     *   - .unauthorized
     *   - .notAcceptable
     *   - .notFound
     *
     * @param {Number} status
     * @api private
     */

    Response.prototype._setStatusProperties = function(status){
      // handle IE9 bug: http://stackoverflow.com/questions/10046972/msie-returns-status-code-of-1223-for-ajax-request
      if (status === 1223) {
        status = 204;
      }

      var type = status / 100 | 0;

      // status / class
      this.status = this.statusCode = status;
      this.statusType = type;

      // basics
      this.info = 1 == type;
      this.ok = 2 == type;
      this.clientError = 4 == type;
      this.serverError = 5 == type;
      this.error = (4 == type || 5 == type)
        ? this.toError()
        : false;

      // sugar
      this.accepted = 202 == status;
      this.noContent = 204 == status;
      this.badRequest = 400 == status;
      this.unauthorized = 401 == status;
      this.notAcceptable = 406 == status;
      this.notFound = 404 == status;
      this.forbidden = 403 == status;
    };

    /**
     * Return an `Error` representative of this response.
     *
     * @return {Error}
     * @api public
     */

    Response.prototype.toError = function(){
      var req = this.req;
      var method = req.method;
      var url = req.url;

      var msg = 'cannot ' + method + ' ' + url + ' (' + this.status + ')';
      var err = new Error(msg);
      err.status = this.status;
      err.method = method;
      err.url = url;

      return err;
    };

    /**
     * Expose `Response`.
     */

    request.Response = Response;

    /**
     * Initialize a new `Request` with the given `method` and `url`.
     *
     * @param {String} method
     * @param {String} url
     * @api public
     */

    function Request(method, url) {
      var self = this;
      this._query = this._query || [];
      this.method = method;
      this.url = url;
      this.header = {}; // preserves header name case
      this._header = {}; // coerces header names to lowercase
      this.on('end', function(){
        var err = null;
        var res = null;

        try {
          res = new Response(self);
        } catch(e) {
          err = new Error('Parser is unable to parse the response');
          err.parse = true;
          err.original = e;
          // issue #675: return the raw response if the response parsing fails
          err.rawResponse = self.xhr && self.xhr.responseText ? self.xhr.responseText : null;
          // issue #876: return the http status code if the response parsing fails
          err.statusCode = self.xhr && self.xhr.status ? self.xhr.status : null;
          return self.callback(err);
        }

        self.emit('response', res);

        var new_err;
        try {
          if (res.status < 200 || res.status >= 300) {
            new_err = new Error(res.statusText || 'Unsuccessful HTTP response');
            new_err.original = err;
            new_err.response = res;
            new_err.status = res.status;
          }
        } catch(e) {
          new_err = e; // #985 touching res may cause INVALID_STATE_ERR on old Android
        }

        // #1000 don't catch errors from the callback to avoid double calling it
        if (new_err) {
          self.callback(new_err, res);
        } else {
          self.callback(null, res);
        }
      });
    }

    /**
     * Mixin `Emitter` and `requestBase`.
     */

    Emitter(Request.prototype);
    for (var key in requestBase) {
      Request.prototype[key] = requestBase[key];
    }

    /**
     * Set Content-Type to `type`, mapping values from `request.types`.
     *
     * Examples:
     *
     *      superagent.types.xml = 'application/xml';
     *
     *      request.post('/')
     *        .type('xml')
     *        .send(xmlstring)
     *        .end(callback);
     *
     *      request.post('/')
     *        .type('application/xml')
     *        .send(xmlstring)
     *        .end(callback);
     *
     * @param {String} type
     * @return {Request} for chaining
     * @api public
     */

    Request.prototype.type = function(type){
      this.set('Content-Type', request.types[type] || type);
      return this;
    };

    /**
     * Set responseType to `val`. Presently valid responseTypes are 'blob' and
     * 'arraybuffer'.
     *
     * Examples:
     *
     *      req.get('/')
     *        .responseType('blob')
     *        .end(callback);
     *
     * @param {String} val
     * @return {Request} for chaining
     * @api public
     */

    Request.prototype.responseType = function(val){
      this._responseType = val;
      return this;
    };

    /**
     * Set Accept to `type`, mapping values from `request.types`.
     *
     * Examples:
     *
     *      superagent.types.json = 'application/json';
     *
     *      request.get('/agent')
     *        .accept('json')
     *        .end(callback);
     *
     *      request.get('/agent')
     *        .accept('application/json')
     *        .end(callback);
     *
     * @param {String} accept
     * @return {Request} for chaining
     * @api public
     */

    Request.prototype.accept = function(type){
      this.set('Accept', request.types[type] || type);
      return this;
    };

    /**
     * Set Authorization field value with `user` and `pass`.
     *
     * @param {String} user
     * @param {String} pass
     * @param {Object} options with 'type' property 'auto' or 'basic' (default 'basic')
     * @return {Request} for chaining
     * @api public
     */

    Request.prototype.auth = function(user, pass, options){
      if (!options) {
        options = {
          type: 'basic'
        }
      }

      switch (options.type) {
        case 'basic':
          var str = btoa(user + ':' + pass);
          this.set('Authorization', 'Basic ' + str);
        break;

        case 'auto':
          this.username = user;
          this.password = pass;
        break;
      }
      return this;
    };

    /**
    * Add query-string `val`.
    *
    * Examples:
    *
    *   request.get('/shoes')
    *     .query('size=10')
    *     .query({ color: 'blue' })
    *
    * @param {Object|String} val
    * @return {Request} for chaining
    * @api public
    */

    Request.prototype.query = function(val){
      if ('string' != typeof val) val = serialize(val);
      if (val) this._query.push(val);
      return this;
    };

    /**
     * Queue the given `file` as an attachment to the specified `field`,
     * with optional `filename`.
     *
     * ``` js
     * request.post('/upload')
     *   .attach('content', new Blob(['<a id="a"><b id="b">hey!</b></a>'], { type: "text/html"}))
     *   .end(callback);
     * ```
     *
     * @param {String} field
     * @param {Blob|File} file
     * @param {String} filename
     * @return {Request} for chaining
     * @api public
     */

    Request.prototype.attach = function(field, file, filename){
      this._getFormData().append(field, file, filename || file.name);
      return this;
    };

    Request.prototype._getFormData = function(){
      if (!this._formData) {
        this._formData = new root.FormData();
      }
      return this._formData;
    };

    /**
     * Invoke the callback with `err` and `res`
     * and handle arity check.
     *
     * @param {Error} err
     * @param {Response} res
     * @api private
     */

    Request.prototype.callback = function(err, res){
      var fn = this._callback;
      this.clearTimeout();
      fn(err, res);
    };

    /**
     * Invoke callback with x-domain error.
     *
     * @api private
     */

    Request.prototype.crossDomainError = function(){
      var err = new Error('Request has been terminated\nPossible causes: the network is offline, Origin is not allowed by Access-Control-Allow-Origin, the page is being unloaded, etc.');
      err.crossDomain = true;

      err.status = this.status;
      err.method = this.method;
      err.url = this.url;

      this.callback(err);
    };

    /**
     * Invoke callback with timeout error.
     *
     * @api private
     */

    Request.prototype._timeoutError = function(){
      var timeout = this._timeout;
      var err = new Error('timeout of ' + timeout + 'ms exceeded');
      err.timeout = timeout;
      this.callback(err);
    };

    /**
     * Compose querystring to append to req.url
     *
     * @api private
     */

    Request.prototype._appendQueryString = function(){
      var query = this._query.join('&');
      if (query) {
        this.url += ~this.url.indexOf('?')
          ? '&' + query
          : '?' + query;
      }
    };

    /**
     * Initiate request, invoking callback `fn(res)`
     * with an instanceof `Response`.
     *
     * @param {Function} fn
     * @return {Request} for chaining
     * @api public
     */

    Request.prototype.end = function(fn){
      var self = this;
      var xhr = this.xhr = request.getXHR();
      var timeout = this._timeout;
      var data = this._formData || this._data;

      // store callback
      this._callback = fn || noop;

      // state change
      xhr.onreadystatechange = function(){
        if (4 != xhr.readyState) return;

        // In IE9, reads to any property (e.g. status) off of an aborted XHR will
        // result in the error "Could not complete the operation due to error c00c023f"
        var status;
        try { status = xhr.status } catch(e) { status = 0; }

        if (0 == status) {
          if (self.timedout) return self._timeoutError();
          if (self._aborted) return;
          return self.crossDomainError();
        }
        self.emit('end');
      };

      // progress
      var handleProgress = function(direction, e) {
        if (e.total > 0) {
          e.percent = e.loaded / e.total * 100;
        }
        e.direction = direction;
        self.emit('progress', e);
      }
      if (this.hasListeners('progress')) {
        try {
          xhr.onprogress = handleProgress.bind(null, 'download');
          if (xhr.upload) {
            xhr.upload.onprogress = handleProgress.bind(null, 'upload');
          }
        } catch(e) {
          // Accessing xhr.upload fails in IE from a web worker, so just pretend it doesn't exist.
          // Reported here:
          // https://connect.microsoft.com/IE/feedback/details/837245/xmlhttprequest-upload-throws-invalid-argument-when-used-from-web-worker-context
        }
      }

      // timeout
      if (timeout && !this._timer) {
        this._timer = setTimeout(function(){
          self.timedout = true;
          self.abort();
        }, timeout);
      }

      // querystring
      this._appendQueryString();

      // initiate request
      if (this.username && this.password) {
        xhr.open(this.method, this.url, true, this.username, this.password);
      } else {
        xhr.open(this.method, this.url, true);
      }

      // CORS
      if (this._withCredentials) xhr.withCredentials = true;

      // body
      if ('GET' != this.method && 'HEAD' != this.method && 'string' != typeof data && !this._isHost(data)) {
        // serialize stuff
        var contentType = this._header['content-type'];
        var serialize = this._serializer || request.serialize[contentType ? contentType.split(';')[0] : ''];
        if (!serialize && isJSON(contentType)) serialize = request.serialize['application/json'];
        if (serialize) data = serialize(data);
      }

      // set header fields
      for (var field in this.header) {
        if (null == this.header[field]) continue;
        xhr.setRequestHeader(field, this.header[field]);
      }

      if (this._responseType) {
        xhr.responseType = this._responseType;
      }

      // send stuff
      this.emit('request', this);

      // IE11 xhr.send(undefined) sends 'undefined' string as POST payload (instead of nothing)
      // We need null here if data is undefined
      xhr.send(typeof data !== 'undefined' ? data : null);
      return this;
    };


    /**
     * Expose `Request`.
     */

    request.Request = Request;

    /**
     * GET `url` with optional callback `fn(res)`.
     *
     * @param {String} url
     * @param {Mixed|Function} [data] or fn
     * @param {Function} [fn]
     * @return {Request}
     * @api public
     */

    request.get = function(url, data, fn){
      var req = request('GET', url);
      if ('function' == typeof data) fn = data, data = null;
      if (data) req.query(data);
      if (fn) req.end(fn);
      return req;
    };

    /**
     * HEAD `url` with optional callback `fn(res)`.
     *
     * @param {String} url
     * @param {Mixed|Function} [data] or fn
     * @param {Function} [fn]
     * @return {Request}
     * @api public
     */

    request.head = function(url, data, fn){
      var req = request('HEAD', url);
      if ('function' == typeof data) fn = data, data = null;
      if (data) req.send(data);
      if (fn) req.end(fn);
      return req;
    };

    /**
     * OPTIONS query to `url` with optional callback `fn(res)`.
     *
     * @param {String} url
     * @param {Mixed|Function} [data] or fn
     * @param {Function} [fn]
     * @return {Request}
     * @api public
     */

    request.options = function(url, data, fn){
      var req = request('OPTIONS', url);
      if ('function' == typeof data) fn = data, data = null;
      if (data) req.send(data);
      if (fn) req.end(fn);
      return req;
    };

    /**
     * DELETE `url` with optional callback `fn(res)`.
     *
     * @param {String} url
     * @param {Function} [fn]
     * @return {Request}
     * @api public
     */

    function del(url, fn){
      var req = request('DELETE', url);
      if (fn) req.end(fn);
      return req;
    };

    request['del'] = del;
    request['delete'] = del;

    /**
     * PATCH `url` with optional `data` and callback `fn(res)`.
     *
     * @param {String} url
     * @param {Mixed} [data]
     * @param {Function} [fn]
     * @return {Request}
     * @api public
     */

    request.patch = function(url, data, fn){
      var req = request('PATCH', url);
      if ('function' == typeof data) fn = data, data = null;
      if (data) req.send(data);
      if (fn) req.end(fn);
      return req;
    };

    /**
     * POST `url` with optional `data` and callback `fn(res)`.
     *
     * @param {String} url
     * @param {Mixed} [data]
     * @param {Function} [fn]
     * @return {Request}
     * @api public
     */

    request.post = function(url, data, fn){
      var req = request('POST', url);
      if ('function' == typeof data) fn = data, data = null;
      if (data) req.send(data);
      if (fn) req.end(fn);
      return req;
    };

    /**
     * PUT `url` with optional `data` and callback `fn(res)`.
     *
     * @param {String} url
     * @param {Mixed|Function} [data] or fn
     * @param {Function} [fn]
     * @return {Request}
     * @api public
     */

    request.put = function(url, data, fn){
      var req = request('PUT', url);
      if ('function' == typeof data) fn = data, data = null;
      if (data) req.send(data);
      if (fn) req.end(fn);
      return req;
    };


/***/ },
/* 44 */
/***/ function(module, exports, __webpack_require__) {

    
    /**
     * Expose `Emitter`.
     */

    if (true) {
      module.exports = Emitter;
    }

    /**
     * Initialize a new `Emitter`.
     *
     * @api public
     */

    function Emitter(obj) {
      if (obj) return mixin(obj);
    };

    /**
     * Mixin the emitter properties.
     *
     * @param {Object} obj
     * @return {Object}
     * @api private
     */

    function mixin(obj) {
      for (var key in Emitter.prototype) {
        obj[key] = Emitter.prototype[key];
      }
      return obj;
    }

    /**
     * Listen on the given `event` with `fn`.
     *
     * @param {String} event
     * @param {Function} fn
     * @return {Emitter}
     * @api public
     */

    Emitter.prototype.on =
    Emitter.prototype.addEventListener = function(event, fn){
      this._callbacks = this._callbacks || {};
      (this._callbacks['$' + event] = this._callbacks['$' + event] || [])
        .push(fn);
      return this;
    };

    /**
     * Adds an `event` listener that will be invoked a single
     * time then automatically removed.
     *
     * @param {String} event
     * @param {Function} fn
     * @return {Emitter}
     * @api public
     */

    Emitter.prototype.once = function(event, fn){
      function on() {
        this.off(event, on);
        fn.apply(this, arguments);
      }

      on.fn = fn;
      this.on(event, on);
      return this;
    };

    /**
     * Remove the given callback for `event` or all
     * registered callbacks.
     *
     * @param {String} event
     * @param {Function} fn
     * @return {Emitter}
     * @api public
     */

    Emitter.prototype.off =
    Emitter.prototype.removeListener =
    Emitter.prototype.removeAllListeners =
    Emitter.prototype.removeEventListener = function(event, fn){
      this._callbacks = this._callbacks || {};

      // all
      if (0 == arguments.length) {
        this._callbacks = {};
        return this;
      }

      // specific event
      var callbacks = this._callbacks['$' + event];
      if (!callbacks) return this;

      // remove all handlers
      if (1 == arguments.length) {
        delete this._callbacks['$' + event];
        return this;
      }

      // remove specific handler
      var cb;
      for (var i = 0; i < callbacks.length; i++) {
        cb = callbacks[i];
        if (cb === fn || cb.fn === fn) {
          callbacks.splice(i, 1);
          break;
        }
      }
      return this;
    };

    /**
     * Emit `event` with the given args.
     *
     * @param {String} event
     * @param {Mixed} ...
     * @return {Emitter}
     */

    Emitter.prototype.emit = function(event){
      this._callbacks = this._callbacks || {};
      var args = [].slice.call(arguments, 1)
        , callbacks = this._callbacks['$' + event];

      if (callbacks) {
        callbacks = callbacks.slice(0);
        for (var i = 0, len = callbacks.length; i < len; ++i) {
          callbacks[i].apply(this, args);
        }
      }

      return this;
    };

    /**
     * Return array of callbacks for `event`.
     *
     * @param {String} event
     * @return {Array}
     * @api public
     */

    Emitter.prototype.listeners = function(event){
      this._callbacks = this._callbacks || {};
      return this._callbacks['$' + event] || [];
    };

    /**
     * Check if this emitter has `event` handlers.
     *
     * @param {String} event
     * @return {Boolean}
     * @api public
     */

    Emitter.prototype.hasListeners = function(event){
      return !! this.listeners(event).length;
    };


/***/ },
/* 45 */
/***/ function(module, exports, __webpack_require__) {

    /**
     * Module of mixed-in functions shared between node and client code
     */
    var isObject = __webpack_require__(46);

    /**
     * Clear previous timeout.
     *
     * @return {Request} for chaining
     * @api public
     */

    exports.clearTimeout = function _clearTimeout(){
      this._timeout = 0;
      clearTimeout(this._timer);
      return this;
    };

    /**
     * Override default response body parser
     *
     * This function will be called to convert incoming data into request.body
     *
     * @param {Function}
     * @api public
     */

    exports.parse = function parse(fn){
      this._parser = fn;
      return this;
    };

    /**
     * Override default request body serializer
     *
     * This function will be called to convert data set via .send or .attach into payload to send
     *
     * @param {Function}
     * @api public
     */

    exports.serialize = function serialize(fn){
      this._serializer = fn;
      return this;
    };

    /**
     * Set timeout to `ms`.
     *
     * @param {Number} ms
     * @return {Request} for chaining
     * @api public
     */

    exports.timeout = function timeout(ms){
      this._timeout = ms;
      return this;
    };

    /**
     * Promise support
     *
     * @param {Function} resolve
     * @param {Function} reject
     * @return {Request}
     */

    exports.then = function then(resolve, reject) {
      if (!this._fullfilledPromise) {
        var self = this;
        this._fullfilledPromise = new Promise(function(innerResolve, innerReject){
          self.end(function(err, res){
            if (err) innerReject(err); else innerResolve(res);
          });
        });
      }
      return this._fullfilledPromise.then(resolve, reject);
    }

    exports.catch = function(cb) {
      return this.then(undefined, cb);
    };

    /**
     * Allow for extension
     */

    exports.use = function use(fn) {
      fn(this);
      return this;
    }


    /**
     * Get request header `field`.
     * Case-insensitive.
     *
     * @param {String} field
     * @return {String}
     * @api public
     */

    exports.get = function(field){
      return this._header[field.toLowerCase()];
    };

    /**
     * Get case-insensitive header `field` value.
     * This is a deprecated internal API. Use `.get(field)` instead.
     *
     * (getHeader is no longer used internally by the superagent code base)
     *
     * @param {String} field
     * @return {String}
     * @api private
     * @deprecated
     */

    exports.getHeader = exports.get;

    /**
     * Set header `field` to `val`, or multiple fields with one object.
     * Case-insensitive.
     *
     * Examples:
     *
     *      req.get('/')
     *        .set('Accept', 'application/json')
     *        .set('X-API-Key', 'foobar')
     *        .end(callback);
     *
     *      req.get('/')
     *        .set({ Accept: 'application/json', 'X-API-Key': 'foobar' })
     *        .end(callback);
     *
     * @param {String|Object} field
     * @param {String} val
     * @return {Request} for chaining
     * @api public
     */

    exports.set = function(field, val){
      if (isObject(field)) {
        for (var key in field) {
          this.set(key, field[key]);
        }
        return this;
      }
      this._header[field.toLowerCase()] = val;
      this.header[field] = val;
      return this;
    };

    /**
     * Remove header `field`.
     * Case-insensitive.
     *
     * Example:
     *
     *      req.get('/')
     *        .unset('User-Agent')
     *        .end(callback);
     *
     * @param {String} field
     */
    exports.unset = function(field){
      delete this._header[field.toLowerCase()];
      delete this.header[field];
      return this;
    };

    /**
     * Write the field `name` and `val`, or multiple fields with one object
     * for "multipart/form-data" request bodies.
     *
     * ``` js
     * request.post('/upload')
     *   .field('foo', 'bar')
     *   .end(callback);
     *
     * request.post('/upload')
     *   .field({ foo: 'bar', baz: 'qux' })
     *   .end(callback);
     * ```
     *
     * @param {String|Object} name
     * @param {String|Blob|File|Buffer|fs.ReadStream} val
     * @return {Request} for chaining
     * @api public
     */
    exports.field = function(name, val) {

      // name should be either a string or an object.
      if (null === name ||  undefined === name) {
        throw new Error('.field(name, val) name can not be empty');
      }

      if (isObject(name)) {
        for (var key in name) {
          this.field(key, name[key]);
        }
        return this;
      }

      // val should be defined now
      if (null === val || undefined === val) {
        throw new Error('.field(name, val) val can not be empty');
      }
      this._getFormData().append(name, val);
      return this;
    };

    /**
     * Abort the request, and clear potential timeout.
     *
     * @return {Request}
     * @api public
     */
    exports.abort = function(){
      if (this._aborted) {
        return this;
      }
      this._aborted = true;
      this.xhr && this.xhr.abort(); // browser
      this.req && this.req.abort(); // node
      this.clearTimeout();
      this.emit('abort');
      return this;
    };

    /**
     * Enable transmission of cookies with x-domain requests.
     *
     * Note that for this to work the origin must not be
     * using "Access-Control-Allow-Origin" with a wildcard,
     * and also must set "Access-Control-Allow-Credentials"
     * to "true".
     *
     * @api public
     */

    exports.withCredentials = function(){
      // This is browser-only functionality. Node side is no-op.
      this._withCredentials = true;
      return this;
    };

    /**
     * Set the max redirects to `n`. Does noting in browser XHR implementation.
     *
     * @param {Number} n
     * @return {Request} for chaining
     * @api public
     */

    exports.redirects = function(n){
      this._maxRedirects = n;
      return this;
    };

    /**
     * Convert to a plain javascript object (not JSON string) of scalar properties.
     * Note as this method is designed to return a useful non-this value,
     * it cannot be chained.
     *
     * @return {Object} describing method, url, and data of this request
     * @api public
     */

    exports.toJSON = function(){
      return {
        method: this.method,
        url: this.url,
        data: this._data,
        headers: this._header
      };
    };

    /**
     * Check if `obj` is a host object,
     * we don't want to serialize these :)
     *
     * TODO: future proof, move to compoent land
     *
     * @param {Object} obj
     * @return {Boolean}
     * @api private
     */

    exports._isHost = function _isHost(obj) {
      var str = {}.toString.call(obj);

      switch (str) {
        case '[object File]':
        case '[object Blob]':
        case '[object FormData]':
          return true;
        default:
          return false;
      }
    }

    /**
     * Send `data` as the request body, defaulting the `.type()` to "json" when
     * an object is given.
     *
     * Examples:
     *
     *       // manual json
     *       request.post('/user')
     *         .type('json')
     *         .send('{"name":"tj"}')
     *         .end(callback)
     *
     *       // auto json
     *       request.post('/user')
     *         .send({ name: 'tj' })
     *         .end(callback)
     *
     *       // manual x-www-form-urlencoded
     *       request.post('/user')
     *         .type('form')
     *         .send('name=tj')
     *         .end(callback)
     *
     *       // auto x-www-form-urlencoded
     *       request.post('/user')
     *         .type('form')
     *         .send({ name: 'tj' })
     *         .end(callback)
     *
     *       // defaults to x-www-form-urlencoded
     *      request.post('/user')
     *        .send('name=tobi')
     *        .send('species=ferret')
     *        .end(callback)
     *
     * @param {String|Object} data
     * @return {Request} for chaining
     * @api public
     */

    exports.send = function(data){
      var obj = isObject(data);
      var type = this._header['content-type'];

      // merge
      if (obj && isObject(this._data)) {
        for (var key in data) {
          this._data[key] = data[key];
        }
      } else if ('string' == typeof data) {
        // default to x-www-form-urlencoded
        if (!type) this.type('form');
        type = this._header['content-type'];
        if ('application/x-www-form-urlencoded' == type) {
          this._data = this._data
            ? this._data + '&' + data
            : data;
        } else {
          this._data = (this._data || '') + data;
        }
      } else {
        this._data = data;
      }

      if (!obj || this._isHost(data)) return this;

      // default to json
      if (!type) this.type('json');
      return this;
    };


/***/ },
/* 46 */
/***/ function(module, exports) {

    /**
     * Check if `obj` is an object.
     *
     * @param {Object} obj
     * @return {Boolean}
     * @api private
     */

    function isObject(obj) {
      return null !== obj && 'object' === typeof obj;
    }

    module.exports = isObject;


/***/ },
/* 47 */
/***/ function(module, exports) {

    // The node and browser modules expose versions of this with the
    // appropriate constructor function bound as first argument
    /**
     * Issue a request:
     *
     * Examples:
     *
     *    request('GET', '/users').end(callback)
     *    request('/users').end(callback)
     *    request('/users', callback)
     *
     * @param {String} method
     * @param {String|Function} url or callback
     * @return {Request}
     * @api public
     */

    function request(RequestConstructor, method, url) {
      // callback
      if ('function' == typeof url) {
        return new RequestConstructor('GET', method).end(url);
      }

      // url first
      if (2 == arguments.length) {
        return new RequestConstructor('GET', method);
      }

      return new RequestConstructor(method, url);
    }

    module.exports = request;


/***/ }
/******/ ])
});

var COMET = (function(){
    var config = {
        
    };
    cometservice = new CometService({
        subscribeKey: "<?php echo KEY_B; ?>",
        publishKey: "<?php echo KEY_A; ?>" ,
        ssl: (window.location.protocol=='https:') ? true : false
    });
    function processChannel(channel){
        while(channel.charAt(0) === '/'){
            channel = channel.substr(1);
        }
        return channel;
    };
    return {
        init:function(params){
            cometservice.addListener({
                    message: function(m) {
                    // handle message
                    var channelName = m.channel; // The channel for which the message belongs
                    var channelGroup = m.subscription; // The channel group or wildcard subscription match (if exists)
                    var pubTT = m.timetoken; // Publish timetoken
                    var msg = m.message; // The Payload
                    if(msg.hasOwnProperty('fromid') || (msg.hasOwnProperty('grp') && msg.grp==1)){
                        chatroomcall_callback(msg);
                    }else{
                        cometcall_callback(msg);
                    }
                }
            });
            return {
                subscribe: function(params,callback){
                    var channel = params.channel;
                    if(channel==undefined || channel==0 || channel.trim()==""){
                        console.log('empty channel');
                        return;
                    }
                    cometservice.subscribe({
                        channels: [processChannel(channel)],
                        withPresence: false
                    })
                },
                unsubscribe:function(params){
                    var channel = params.channel;
                    if(channel==undefined || channel==0 || channel.trim()==""){
                        console.log('empty channel');
                        return;
                    }
                    cometservice.unsubscribe({
                        channels: [processChannel(channel)]
                    })
                },
                terminate: function(){
                    cometservice.unsubscribeAll();
                    cometservice.stop();
                }
            }
        },
        publish: function(params,callback){
            var channel = params.channel;
            if(channel==undefined || channel==0 || channel.trim()==""){
                return;
            }
            if(params.hasOwnProperty('data') && !params.hasOwnProperty('message')){
                params.message = params.data;
            }
            params.channel = processChannel(channel);
            cometservice.publish(params,callback);
        }
    }
})();

<?php } else { ?>


(window['JSON'] && window['JSON']['stringify']) || (function () {
    window['JSON'] || (window['JSON'] = {});

    function toJSON(key) {
        try      { return this.valueOf() }
        catch(e) { return null }
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;

    function quote(string) {
        escapable.lastIndex = 0;
        return escapable.test(string) ?
            '"'+string.replace(escapable, function (a) {
                var c = meta[a];
                return typeof c === 'string' ? c :
                    '\\u'+('0000'+a.charCodeAt(0).toString(16)).slice(-4);
            })+'"' :
            '"'+string+'"';
    }

    function str(key, holder) {
        var i,
            k,
            v,
            length,
            partial,
            mind  = gap,
            value = holder[key];

        if (value && typeof value === 'object') {
            value = toJSON.call( value, key );
        }

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':
            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':
            return String(value);

        case 'object':

            if (!value) {
                return 'null';
            }

            gap+= indent;
            partial = [];

            if (Object.prototype.toString.apply(value) === '[object Array]') {

                length = value.length;
                for (i = 0; i < length; i+= 1) {
                    partial[i] = str(i, value) || 'null';
                }

                v = partial.length === 0 ? '[]' :
                    gap ? '[\n'+gap+
                            partial.join(',\n'+gap)+'\n'+
                                mind+']' :
                          '['+partial.join(',')+']';
                gap = mind;
                return v;
            }
            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i+= 1) {
                    k = rep[i];
                    if (typeof k === 'string') {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k)+(gap ? ': ' : ':')+v);
                        }
                    }
                }
            } else {
                for (k in value) {
                    if (Object.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k)+(gap ? ': ' : ':')+v);
                        }
                    }
                }
            }

            v = partial.length === 0 ? '{}' :
                gap ? '{\n'+gap+partial.join(',\n'+gap)+'\n'+
                        mind+'}' : '{'+partial.join(',')+'}';
            gap = mind;
            return v;
        }
    }

    if (typeof JSON['stringify'] !== 'function') {
        JSON['stringify'] = function (value, replacer, space) {
            var i;
            gap = '';
            indent = '';

            if (typeof space === 'number') {
                for (i = 0; i < space; i+= 1) {
                    indent+= ' ';
                }
            } else if (typeof space === 'string') {
                indent = space;
            }
            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                     typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }
            return str('', {'': value});
        };
    }

    if (typeof JSON['parse'] !== 'function') {

        JSON['parse'] = function (text) {return eval('('+text+')')};
    }
}());
var NOW             = 1
,   READY           = false
,   READY_BUFFER    = []
,   PRESENCE_SUFFIX = '-pnpres'
,   DEF_WINDOWING   = 10
,   DEF_TIMEOUT     = 10000
,   DEF_SUB_TIMEOUT = 310
,   DEF_KEEPALIVE   = 60
,   SECOND          = 1000
,   URLBIT          = '/'
,   PARAMSBIT       = '&'
,   PRESENCE_HB_THRESHOLD = 5
,   PRESENCE_HB_DEFAULT  = 30
,   SDK_VER         = '3.6.7'
,   REPL            = /{([\w\-]+)}/g;


function unique() { return'x'+NOW+''+(+new Date) }
function rnow()   { return+new Date }


var nextorigin = (function() {
    var max = 20
    ,   ori = Math.floor(Math.random() * max);
    return function( origin, failover ) {
        return origin.indexOf('pubsub.') > 0
            && origin.replace(
             'pubsub', 'ps'+(
                failover ? uuid().split('-')[0] :
                (++ori < max? ori : ori=1)
            ) ) || origin;
    }
})();



function build_url( url_components, url_params ) {
    var url    = url_components.join(URLBIT)
    ,   params = [];

    if (!url_params) return url;

    each( url_params, function( key, value ) {
        var value_str = (typeof value == 'object')?JSON['stringify'](value):value;
        (typeof value != 'undefined' &&
            value != null && encode(value_str).length > 0
        ) && params.push(key+"="+encode(value_str));
    } );

    url+= "?"+params.join(PARAMSBIT);
    return url;
}


function updater( fun, rate ) {
    var timeout
    ,   last   = 0
    ,   runnit = function() {
        if (last+rate > rnow()) {
            clearTimeout(timeout);
            timeout = setTimeout( runnit, rate );
        }
        else {
            last = rnow();
            fun();
        }
    };

    return runnit;
}


function grep( list, fun ) {
    var fin = [];
    each( list || [], function(l) { fun(l) && fin.push(l) } );
    return fin
}


function supplant( str, values ) {
    return str.replace( REPL, function( _, match ) {
        return values[match] || _
    } );
}

function uuid(callback) {
    var u = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g,
    function(c) {
        var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
        return v.toString(16);
    });
    if (callback) callback(u);
    return u;
}

function isArray(arg) {
  return !!arg && (Array.isArray && Array.isArray(arg) || typeof(arg.length) === "number")
}


function each( o, f) {
    if ( !o || !f ) return;

    if ( isArray(o) )
        for ( var i = 0, l = o.length; i < l; )
            f.call( o[i], o[i], i++);
    else
        for ( var i in o )
            o.hasOwnProperty    &&
            o.hasOwnProperty(i) &&
            f.call( o[i], i, o[i] );
}

function map( list, fun ) {
    var fin = [];
    each( list || [], function( k, v ) { fin.push(fun( k, v )) } );
    return fin;
}


function encode(path) { return encodeURIComponent(path) }

function generate_channel_list(channels, nopresence) {
    var list = [];
    each( channels, function( channel, status ) {
        if (nopresence) {
            if(channel.search('-pnpres') < 0) {
                if (status.subscribed) list.push(channel);
            }
        } else {
            if (status.subscribed) list.push(channel);
        }
    });
    return list.sort();
}



function ready() { setTimeout( function() {
    if (typeof(cometready) !== 'undefined' ) {
        if(!((window.top!=window.self)&&('<?php echo $theme; ?>'=='docked'))){
            cometready();
        }
    }
    if (typeof(cometchatroomready) !== 'undefined' ) {
        cometchatroomready();
    }
    if (typeof(chatroomready) !== 'undefined' ) {
        chatroomready();
    }
    if (READY) return;
    READY = 1;
    each( READY_BUFFER, function(connect) { connect() } );
}, SECOND ); }

function PNmessage(args) {
    msg = args || {'apns' : {}},
    msg['getCometMessage'] = function() {
        var m = {};

        if (Object.keys(msg['apns']).length) {
            m['pn_apns'] = {
                    'aps' : {
                        'alert' : msg['apns']['alert'] ,
                        'badge' : msg['apns']['badge']
                    }
            }
            for (var k in msg['apns']) {
                m['pn_apns'][k] = msg['apns'][k];
            }
            var exclude1 = ['badge','alert'];
            for (var k in exclude1) {
                delete m['pn_apns'][exclude1[k]];
            }
        }



        if (msg['gcm']) {
            m['pn_gcm'] = {
                'data' : msg['gcm']
            }
        }

        for (var k in msg) {
            m[k] = msg[k];
        }
        var exclude = ['apns','gcm','publish', 'channel','callback','error'];
        for (var k in exclude) {
            delete m[exclude[k]];
        }

        return m;
    };
    msg['publish'] = function() {

        var m = msg.getCometMessage();

        if (msg['comet'] && msg['channel']) {
            msg['comet'].publish({
                'message' : m,
                'channel' : msg['channel'],
                'callback' : msg['callback'],
                'error' : msg['error']
            })
        }
    };
    return msg;
}

function PN_API(setup) {
    var SUB_WINDOWING = +setup['windowing']   || DEF_WINDOWING
    ,   SUB_TIMEOUT   = (+setup['timeout']     || DEF_SUB_TIMEOUT) * SECOND
    ,   KEEPALIVE     = (+setup['keepalive']   || DEF_KEEPALIVE)   * SECOND
    ,   NOLEAVE       = setup['noleave']       || 0
    ,   PUBLISH_KEY   = '<?php echo KEY_A;?>'
    ,   SUBSCRIBE_KEY = '<?php echo KEY_B;?>'
    ,   AUTH_KEY      = setup['auth_key']      || ''
    ,   SECRET_KEY    = setup['secret_key']    || ''
    ,   hmac_SHA256   = setup['hmac_SHA256']
    ,   SSL           = setup['ssl']            ? 's' : ''
    ,   ORIGIN        = (window.location.protocol=='https:') ? 'https://pubsub.pubnub.com': 'http://'+(setup['origin']||'x3.chatforyoursite.com')
    ,   STD_ORIGIN    = nextorigin(ORIGIN)
    ,   SUB_ORIGIN    = nextorigin(ORIGIN)
    ,   CONNECT       = function(){}
    ,   PUB_QUEUE     = []
    ,   TIME_DRIFT    = 0
    ,   SUB_CALLBACK  = 0
    ,   SUB_CHANNEL   = 0
    ,   SUB_RECEIVER  = 0
    ,   SUB_RESTORE   = setup['restore'] || 0
    ,   SUB_BUFF_WAIT = 0
    ,   TIMETOKEN     = 0
    ,   RESUMED       = false
    ,   CHANNELS      = {}
    ,   STATE         = {}
    ,   PRESENCE_HB_TIMEOUT  = null
    ,   PRESENCE_HB          = validate_presence_heartbeat(setup['heartbeat'] || setup['pnexpires'] || 0, setup['error'])
    ,   PRESENCE_HB_INTERVAL = setup['heartbeat_interval'] || PRESENCE_HB - 3
    ,   PRESENCE_HB_RUNNING  = false
    ,   NO_WAIT_FOR_PENDING  = setup['no_wait_for_pending']
    ,   COMPATIBLE_35 = setup['compatible_3.5']  || false
    ,   xdr           = setup['xdr']
    ,   params        = setup['params'] || {}
    ,   error         = setup['error']      || function() {}
    ,   _is_online    = setup['_is_online'] || function() { return 1 }
    ,   jsonp_cb      = setup['jsonp_cb']   || function() { return 0 }
    ,   db            = setup['db']         || {'get': function(){}, 'set': function(){}}
    ,   CIPHER_KEY    = setup['cipher_key']
    ,   UUID          = setup['uuid'] || ( db && db['get'](SUBSCRIBE_KEY+'uuid') || '');

    var crypto_obj    = setup['crypto_obj'] ||
        {
            'encrypt' : function(a,key){ return a},
            'decrypt' : function(b,key){return b}
        };

    function _get_url_params(data) {
        if (!data) data = {};
        each( params , function( key, value ) {
            if (!(key in data)) data[key] = value;
        });
        return data;
    }

    function _object_to_key_list(o) {
        var l = []
        each( o , function( key, value ) {
            l.push(key);
        });
        return l;
    }
    function _object_to_key_list_sorted(o) {
        return _object_to_key_list(o).sort();
    }

    function _get_pam_sign_input_from_params(params) {
        var si = "";
        var l = _object_to_key_list_sorted(params);

        for (var i in l) {
            var k = l[i]
            si+= k+"="+encode(params[k]) ;
            if (i != l.length - 1) si+= "&"
        }
        return si;
    }

    function validate_presence_heartbeat(heartbeat, cur_heartbeat, error) {
        var err = false;

        if (typeof heartbeat === 'number') {
            if (heartbeat > PRESENCE_HB_THRESHOLD || heartbeat == 0)
                err = false;
            else
                err = true;
        } else if(typeof heartbeat === 'boolean'){
            if (!heartbeat) {
                return 0;
            } else {
                return PRESENCE_HB_DEFAULT;
            }
        } else {
            err = true;
        }

        if (err) {
            error && error("Presence Heartbeat value invalid. Valid range ( x > "+PRESENCE_HB_THRESHOLD+" or x = 0). Current Value : "+(cur_heartbeat || PRESENCE_HB_THRESHOLD));
            return cur_heartbeat || PRESENCE_HB_THRESHOLD;
        } else return heartbeat;
    }

    function encrypt(input, key) {
        return crypto_obj['encrypt'](input, key || CIPHER_KEY) || input;
    }
    function decrypt(input, key) {
        return crypto_obj['decrypt'](input, key || CIPHER_KEY) ||
               crypto_obj['decrypt'](input, CIPHER_KEY) ||
               input;
    }

    function error_common(message, callback) {
        callback && callback({ 'error' : message || "error occurred"});
        error && error(message);
    }
    function _presence_heartbeat() {

        clearTimeout(PRESENCE_HB_TIMEOUT);

        if (!PRESENCE_HB_INTERVAL || PRESENCE_HB_INTERVAL >= 500 || PRESENCE_HB_INTERVAL < 1 || !generate_channel_list(CHANNELS,true).length){
            PRESENCE_HB_RUNNING = false;
            return;
        }

        PRESENCE_HB_RUNNING = true;
        SELF['presence_heartbeat']({
            'callback' : function(r) {
                PRESENCE_HB_TIMEOUT = setTimeout( _presence_heartbeat, (PRESENCE_HB_INTERVAL) * SECOND );
            },
            'error' : function(e) {
                error && error("Presence Heartbeat unable to reach Comet servers."+JSON.stringify(e));
                PRESENCE_HB_TIMEOUT = setTimeout( _presence_heartbeat, (PRESENCE_HB_INTERVAL) * SECOND );
            }
        });
    }

    function start_presence_heartbeat() {
        !PRESENCE_HB_RUNNING && _presence_heartbeat();
    }

    function publish(next) {

        if (NO_WAIT_FOR_PENDING) {
            if (!PUB_QUEUE.length) return;
        } else {
            if (next) PUB_QUEUE.sending = 0;
            if ( PUB_QUEUE.sending || !PUB_QUEUE.length ) return;
            PUB_QUEUE.sending = 1;
        }

        xdr(PUB_QUEUE.shift());
    }

    function each_channel(callback) {
        var count = 0;

        each( generate_channel_list(CHANNELS), function(channel) {
            var chan = CHANNELS[channel];

            if (!chan) return;

            count++;
            (callback||function(){})(chan);
        } );

        return count;
    }
    function _invoke_callback(response, callback, err) {
        if (typeof response == 'object') {
            if (response['error'] && response['message'] && response['payload']) {
                err({'message' : response['message'], 'payload' : response['payload']});
                return;
            }
            if (response['payload']) {
                callback(response['payload']);
                return;
            }
        }
        callback(response);
    }

    function _invoke_error(response,err) {
        if (typeof response == 'object' && response['error'] &&
            response['message'] && response['payload']) {
            err({'message' : response['message'], 'payload' : response['payload']});
        } else err(response);
    }

    var SELF = {
        'LEAVE' : function( channel, blocking, callback, error ) {

            var data   = { 'uuid' : UUID, 'auth' : AUTH_KEY }
            ,   origin = nextorigin(ORIGIN)
            ,   callback = callback || function(){}
            ,   err      = error    || function(){}
            ,   jsonp  = jsonp_cb();


            if (channel.indexOf(PRESENCE_SUFFIX) > 0) return true;

            if (COMPATIBLE_35) {
                if (!SSL)         return false;
                if (jsonp == '0') return false;
            }

            if (NOLEAVE)  return false;

            if (jsonp != '0') data['callback'] = jsonp;

            xdr({
                blocking : blocking || SSL,
                timeout  : 2000,
                callback : jsonp,
                data     : _get_url_params(data),
                success  : function(response) {
                    _invoke_callback(response, callback, err);
                },
                fail     : function(response) {
                    _invoke_error(response, err);
                },
                url      : [
                    origin, 'v2', 'presence', 'sub_key',
                    SUBSCRIBE_KEY, 'channel', encode(channel), 'leave'
                ]
            });
            return true;
        },
        'set_resumed' : function(resumed) {
                RESUMED = resumed;
        },
        'get_cipher_key' : function() {
            return CIPHER_KEY;
        },
        'set_cipher_key' : function(key) {
            CIPHER_KEY = key;
        },
        'raw_encrypt' : function(input, key) {
            return encrypt(input, key);
        },
        'raw_decrypt' : function(input, key) {
            return decrypt(input, key);
        },
        'get_heartbeat' : function() {
            return PRESENCE_HB;
        },
        'set_heartbeat' : function(heartbeat) {
            PRESENCE_HB = validate_presence_heartbeat(heartbeat, PRESENCE_HB_INTERVAL, error);
            PRESENCE_HB_INTERVAL = (PRESENCE_HB - 3 >= 1)?PRESENCE_HB - 3:1;
            CONNECT();
            _presence_heartbeat();
        },
        'get_heartbeat_interval' : function() {
            return PRESENCE_HB_INTERVAL;
        },
        'set_heartbeat_interval' : function(heartbeat_interval) {
            PRESENCE_HB_INTERVAL = heartbeat_interval;
            _presence_heartbeat();
        },
        'get_version' : function() {
            return SDK_VER;
        },
        'getGcmMessageObject' : function(obj) {
            return {
                'data' : obj
            }
        },
        'getApnsMessageObject' : function(obj) {
            var x =  {
                'aps' : { 'badge' : 1, 'alert' : ''}
            }
            for (k in obj) {
                k[x] = obj[k];
            }
            return x;
        },
        'newPnMessage' : function() {
            var x = {};
            if (gcm) x['pn_gcm'] = gcm;
            if (apns) x['pn_apns'] = apns;
            for ( k in n ) {
                x[k] = n[k];
            }
            return x;
        },

        '_add_param' : function(key,val) {
            params[key] = val;
        },

        'history' : function( args, callback ) {
            var callback         = args['callback'] || callback
            ,   count            = args['count']    || args['limit'] || 100
            ,   reverse          = args['reverse']  || "false"
            ,   err              = args['error']    || function(){}
            ,   auth_key         = args['auth_key'] || AUTH_KEY
            ,   cipher_key       = args['cipher_key']
            ,   channel          = args['channel']
            ,   start            = args['start']
            ,   end              = args['end']
            ,   include_token    = args['include_token']
            ,   params           = {}
            ,   jsonp            = jsonp_cb();


            if (!channel)       return error('Missing Channel');
            if (!callback)      return error('Missing Callback');
            if (!SUBSCRIBE_KEY) return error('Missing Subscribe Key');

            params['stringtoken'] = 'true';
            params['count']       = count;
            params['reverse']     = reverse;
            params['auth']        = auth_key;

            if (jsonp) params['callback']              = jsonp;
            if (start) params['start']                 = start;
            if (end)   params['end']                   = end;
            if (include_token) params['include_token'] = 'true';


            xdr({
                callback : jsonp,
                data     : _get_url_params(params),
                success  : function(response) {
                    if (typeof response == 'object' && response['error']) {
                        err({'message' : response['message'], 'payload' : response['payload']});
                        return;
                    }
                    var messages = response[0];
                    var decrypted_messages = [];
                    for (var a = 0; a < messages.length; a++) {
                        var new_message = decrypt(messages[a],cipher_key);
                        try {
                            decrypted_messages['push'](JSON['parse'](new_message));
                        } catch (e) {
                            decrypted_messages['push']((new_message));
                        }
                    }
                    callback([decrypted_messages, response[1], response[2]]);
                },
                fail     : function(response) {
                    _invoke_error(response, err);
                },
                url      : [
                    STD_ORIGIN, 'v2', 'history', 'sub-key',
                    SUBSCRIBE_KEY, 'channel', encode(channel)
                ]
            });
        },

        'replay' : function(args, callback) {
            var callback    = callback || args['callback'] || function(){}
            ,   auth_key    = args['auth_key'] || AUTH_KEY
            ,   source      = args['source']
            ,   destination = args['destination']
            ,   stop        = args['stop']
            ,   start       = args['start']
            ,   end         = args['end']
            ,   reverse     = args['reverse']
            ,   limit       = args['limit']
            ,   jsonp       = jsonp_cb()
            ,   data        = {}
            ,   url;


            if (!source)        return error('Missing Source Channel');
            if (!destination)   return error('Missing Destination Channel');
            if (!PUBLISH_KEY)   return error('Missing Publish Key');
            if (!SUBSCRIBE_KEY) return error('Missing Subscribe Key');


            if (jsonp != '0') data['callback'] = jsonp;
            if (stop)         data['stop']     = 'all';
            if (reverse)      data['reverse']  = 'true';
            if (start)        data['start']    = start;
            if (end)          data['end']      = end;
            if (limit)        data['count']    = limit;

            data['auth'] = auth_key;


            url = [
                STD_ORIGIN, 'v1', 'replay',
                PUBLISH_KEY, SUBSCRIBE_KEY,
                source, destination
            ];


            xdr({
                callback : jsonp,
                success  : function(response) {
                    _invoke_callback(response, callback, err);
                },
                fail     : function() { callback([ 0, 'Disconnected' ]) },
                url      : url,
                data     : _get_url_params(data)
            });
        },

        'auth' : function(auth) {
            AUTH_KEY = auth;
            CONNECT();
        },

        'time' : function(callback) {
            var jsonp = jsonp_cb();
            xdr({
                callback : jsonp,
                data     : _get_url_params({ 'uuid' : UUID, 'auth' : AUTH_KEY }),
                timeout  : SECOND * 5,
                url      : [STD_ORIGIN, 'time', jsonp],
                success  : function(response) { callback(response[0]) },
                fail     : function() { callback(0) }
            });
        },

        'publish' : function( args, callback ) {
            if(args['channel'].charAt(0) == "/"){
                args['channel'] = args['channel'].replace('/','');
            }
            if(args.hasOwnProperty('data') && !args.hasOwnProperty('message')){
                args['message'] = args['data'];
            }
            var msg      = args['message'];
            if (!msg) return error('Missing Message');

            var callback = callback || args['callback'] || msg['callback'] || function(){}
            ,   channel  = args['channel'] || msg['channel']
            ,   auth_key = args['auth_key'] || AUTH_KEY
            ,   cipher_key = args['cipher_key']
            ,   err      = args['error'] || msg['error'] || function() {}
            ,   post     = args['post'] || false
            ,   store    = ('store_in_history' in args) ? args['store_in_history']: true
            ,   jsonp    = jsonp_cb()
            ,   add_msg  = 'push'
            ,   url;

            if (args['prepend']) add_msg = 'unshift'

            if (!channel)       return error('Missing Channel');
            if (!PUBLISH_KEY)   return error('Missing Publish Key');
            if (!SUBSCRIBE_KEY) return error('Missing Subscribe Key');

            if (msg['getCometMessage']) {
                msg = msg['getCometMessage']();
            }


            msg = JSON['stringify'](encrypt(msg, cipher_key));


            url = [
                STD_ORIGIN, 'publish',
                PUBLISH_KEY, SUBSCRIBE_KEY,
                0, encode(channel),
                jsonp, encode(msg)
            ];

            params = { 'uuid' : UUID, 'auth' : auth_key }

            if (!store) params['store'] ="0"


            PUB_QUEUE[add_msg]({
                callback : jsonp,
                timeout  : SECOND * 5,
                url      : url,
                data     : _get_url_params(params),
                fail     : function(response){
                    _invoke_error(response, err);
                    publish(1);
                },
                success  : function(response) {
                    _invoke_callback(response, callback, err);
                    publish(1);
                },
                mode     : (post)?'POST':'GET'
            });


            publish();
        },

        'unsubscribe' : function(args, callback) {
            var channel = args['channel']
            ,   callback      = callback            || args['callback'] || function(){}
            ,   err           = args['error']       || function(){};

            TIMETOKEN   = 0;

            channel = map( (
                channel.join ? channel.join(',') : ''+channel
            ).split(','), function(channel) {
                if (!CHANNELS[channel]) return;
                return channel+','+channel+PRESENCE_SUFFIX;
            } ).join(',');

            each( channel.split(','), function(channel) {
                var CB_CALLED = true;
                if (!channel) return;
                if (READY) {
                    CB_CALLED = SELF['LEAVE']( channel, 0 , callback, err);
                }
                if (!CB_CALLED) callback({action : "leave"});
                CHANNELS[channel] = 0;
                if (channel in STATE) delete STATE[channel];
            } );


            CONNECT();
        },

        'subscribe' : function( args, callback ) {
            var channel       = args['channel']
            ,   callback      = callback            || args['callback']
            ,   callback      = callback            || args['message']
            ,   auth_key      = args['auth_key']    || AUTH_KEY
            ,   connect       = args['connect']     || function(){}
            ,   reconnect     = args['reconnect']   || function(){}
            ,   disconnect    = args['disconnect']  || function(){}
            ,   errcb         = args['error']       || function(){}
            ,   idlecb        = args['idle']        || function(){}
            ,   presence      = args['presence']    || 0
            ,   noheresync    = args['noheresync']  || 0
            ,   backfill      = args['backfill']    || 0
            ,   timetoken     = args['timetoken']   || 0
            ,   sub_timeout   = args['timeout']     || SUB_TIMEOUT
            ,   windowing     = args['windowing']   || SUB_WINDOWING
            ,   state         = args['state']
            ,   heartbeat     = args['heartbeat'] || args['pnexpires']
            ,   restore       = args['restore'] || SUB_RESTORE;

            SUB_RESTORE = restore;

            TIMETOKEN = timetoken;

            if (!channel)       return error('Missing Channel');
            if (!callback)      return error('Missing Callback');
            if (!SUBSCRIBE_KEY) return error('Missing Subscribe Key');

            if (heartbeat || heartbeat === 0) {
                SELF['set_heartbeat'](heartbeat);
            }

            each( (channel.join ? channel.join(',') : ''+channel).split(','),
            function(channel) {
                var settings = CHANNELS[channel] || {};

                CHANNELS[SUB_CHANNEL = channel] = {
                    name         : channel,
                    connected    : settings.connected,
                    disconnected : settings.disconnected,
                    subscribed   : 1,
                    callback     : SUB_CALLBACK = callback,
                    'cipher_key' : args['cipher_key'],
                    connect      : connect,
                    disconnect   : disconnect,
                    reconnect    : reconnect
                };
                if (state) {
                    if (channel in state) {
                        STATE[channel] = state[channel];
                    } else {
                        STATE[channel] = state;
                    }
                }

                if (!presence) return;

                SELF['subscribe']({
                    'channel'  : channel+PRESENCE_SUFFIX,
                    'callback' : presence,
                    'restore'  : restore
                });

                if (settings.subscribed) return;

                if (noheresync) return;
                SELF['here_now']({
                    'channel'  : channel,
                    'callback' : function(here) {
                        each( 'uuids' in here ? here['uuids'] : [],
                        function(uid) { presence( {
                            'action'    : 'join',
                            'uuid'      : uid,
                            'timestamp' : Math.floor(rnow() / 1000),
                            'occupancy' : here['occupancy'] || 1
                        }, here, channel ); } );
                    }
                });
            } );

            function _test_connection(success) {
                if (success) {
                    setTimeout( CONNECT, SECOND );
                }
                else {
                    STD_ORIGIN = nextorigin( ORIGIN, 1 );
                    SUB_ORIGIN = nextorigin( ORIGIN, 1 );

                    setTimeout( function() {
                        SELF['time'](_test_connection);
                    }, SECOND );
                }

                each_channel(function(channel){
                    if (success && channel.disconnected) {
                        channel.disconnected = 0;
                        return channel.reconnect(channel.name);
                    }

                    if (!success && !channel.disconnected) {
                        channel.disconnected = 1;
                        channel.disconnect(channel.name);
                    }
                });
            }

            function _connect() {
                var jsonp    = jsonp_cb()
                ,   channels = generate_channel_list(CHANNELS).join(',');

                if (!channels) return;

                _reset_offline();

                var data = _get_url_params({ 'uuid' : UUID, 'auth' : auth_key });

                var st = JSON.stringify(STATE);
                if (st.length > 2) data['state'] = JSON.stringify(STATE);

                if (PRESENCE_HB) data['heartbeat'] = PRESENCE_HB;

                start_presence_heartbeat();
                SUB_RECEIVER = xdr({
                    timeout  : sub_timeout,
                    callback : jsonp,
                    fail     : function(response) {
                        _invoke_error(response, errcb);
                        SELF['time'](_test_connection);
                    },
                    data     : _get_url_params(data),
                    url      : [
                        SUB_ORIGIN, 'subscribe',
                        SUBSCRIBE_KEY, encode(channels),
                        jsonp, TIMETOKEN
                    ],
                    success : function(messages) {

                        if (!messages || (
                            typeof messages == 'object' &&
                            'error' in messages         &&
                            messages['error']
                        )) {
                            errcb(messages['error']);
                            return setTimeout( CONNECT, SECOND );
                        }

                        idlecb(messages[1]);

                        TIMETOKEN = !TIMETOKEN               &&
                                    SUB_RESTORE              &&
                                    db['get'](SUBSCRIBE_KEY) || messages[1];

                        each_channel(function(channel){
                            if (channel.connected) return;
                            channel.connected = 1;
                            channel.connect(channel.name);
                        });

                        if (RESUMED && !SUB_RESTORE) {
                                TIMETOKEN = 0;
                                RESUMED = false;
                                db['set']( SUBSCRIBE_KEY, 0 );
                                setTimeout( _connect, windowing );
                                return;
                        }

                        if (backfill) {
                            TIMETOKEN = 10000;
                            backfill  = 0;
                        }

                        db['set']( SUBSCRIBE_KEY, messages[1] );

                        var next_callback = (function() {
                            var channels = (messages.length>2?messages[2]:map(
                                generate_channel_list(CHANNELS), function(chan) { return map(
                                    Array(messages[0].length)
                                    .join(',').split(','),
                                    function() { return chan; }
                                ) }).join(','));
                            var list = channels.split(',');

                            return function() {
                                var channel = list.shift()||SUB_CHANNEL;
                                return [
                                    (CHANNELS[channel]||{})
                                    .callback||SUB_CALLBACK,
                                    channel.split(PRESENCE_SUFFIX)[0]
                                ];
                            };
                        })();

                        var latency = detect_latency(+messages[1]);
                        each( messages[0], function(msg) {
                            var next = next_callback();
                            var decrypted_msg = decrypt(msg,
                                (CHANNELS[next[1]])?CHANNELS[next[1]]['cipher_key']:null);
                            next[0]( decrypted_msg, messages, next[1], latency);
                        });

                        setTimeout( _connect, windowing );
                    }
                });
            }

            CONNECT = function() {
                _reset_offline();
                setTimeout( _connect, windowing );
            };

            if (!READY) return READY_BUFFER.push(CONNECT);

            CONNECT();
        },

        'here_now' : function( args, callback ) {
            var callback = args['callback'] || callback
            ,   err      = args['error']    || function(){}
            ,   auth_key = args['auth_key'] || AUTH_KEY
            ,   channel  = args['channel']
            ,   jsonp    = jsonp_cb()
            ,   uuids    = ('uuids' in args) ? args['uuids'] : true
            ,   state    = args['state']
            ,   data     = { 'uuid' : UUID, 'auth' : auth_key };

            if (!uuids) data['disable_uuids'] = 1;
            if (state) data['state'] = 1;

            if (!callback)      return error('Missing Callback');
            if (!SUBSCRIBE_KEY) return error('Missing Subscribe Key');

            var url = [
                    STD_ORIGIN, 'v2', 'presence',
                    'sub_key', SUBSCRIBE_KEY
                ];

            channel && url.push('channel') && url.push(encode(channel));

            if (jsonp != '0') { data['callback'] = jsonp; }

            xdr({
                callback : jsonp,
                data     : _get_url_params(data),
                success  : function(response) {
                    _invoke_callback(response, callback, err);
                },
                fail     : function(response) {
                    _invoke_error(response, err);
                },
                url      : url
            });
        },

        'where_now' : function( args, callback ) {
            var callback = args['callback'] || callback
            ,   err      = args['error']    || function(){}
            ,   auth_key = args['auth_key'] || AUTH_KEY
            ,   jsonp    = jsonp_cb()
            ,   uuid     = args['uuid']     || UUID
            ,   data     = { 'auth' : auth_key };

            if (!callback)      return error('Missing Callback');
            if (!SUBSCRIBE_KEY) return error('Missing Subscribe Key');

            if (jsonp != '0') { data['callback'] = jsonp; }

            xdr({
                callback : jsonp,
                data     : _get_url_params(data),
                success  : function(response) {
                    _invoke_callback(response, callback, err);
                },
                fail     : function(response) {
                    _invoke_error(response, err);
                },
                url      : [
                    STD_ORIGIN, 'v2', 'presence',
                    'sub_key', SUBSCRIBE_KEY,
                    'uuid', encode(uuid)
                ]
            });
        },

        'state' : function(args, callback) {
            var callback = args['callback'] || callback || function(r) {}
            ,   err      = args['error']    || function(){}
            ,   auth_key = args['auth_key'] || AUTH_KEY
            ,   jsonp    = jsonp_cb()
            ,   state    = args['state']
            ,   uuid     = args['uuid'] || UUID
            ,   channel  = args['channel']
            ,   url
            ,   data     = _get_url_params({ 'auth' : auth_key });


            if (!SUBSCRIBE_KEY) return error('Missing Subscribe Key');
            if (!uuid) return error('Missing UUID');
            if (!channel) return error('Missing Channel');

            if (jsonp != '0') { data['callback'] = jsonp; }

            if (CHANNELS[channel] && CHANNELS[channel].subscribed && state) STATE[channel] = state;

            data['state'] = JSON.stringify(state);

            if (state) {
                url      = [
                    STD_ORIGIN, 'v2', 'presence',
                    'sub-key', SUBSCRIBE_KEY,
                    'channel', encode(channel),
                    'uuid', uuid, 'data'
                ]
            } else {
                url      = [
                    STD_ORIGIN, 'v2', 'presence',
                    'sub-key', SUBSCRIBE_KEY,
                    'channel', encode(channel),
                    'uuid', encode(uuid)
                ]
            }

            xdr({
                callback : jsonp,
                data     : _get_url_params(data),
                success  : function(response) {
                    _invoke_callback(response, callback, err);
                },
                fail     : function(response) {
                    _invoke_error(response, err);
                },
                url      : url

            });

        },

        'grant' : function( args, callback ) {
            var callback = args['callback'] || callback
            ,   err      = args['error']    || function(){}
            ,   channel  = args['channel']
            ,   jsonp    = jsonp_cb()
            ,   ttl      = args['ttl']
            ,   r        = (args['read'] )?"1":"0"
            ,   w        = (args['write'])?"1":"0"
            ,   auth_key = args['auth_key'];

            if (!callback)      return error('Missing Callback');
            if (!SUBSCRIBE_KEY) return error('Missing Subscribe Key');
            if (!PUBLISH_KEY)   return error('Missing Publish Key');
            if (!SECRET_KEY)    return error('Missing Secret Key');

            var timestamp  = Math.floor(new Date().getTime() / 1000)
            ,   sign_input = SUBSCRIBE_KEY+"\n"+PUBLISH_KEY+"\n"+"grant"+"\n";

            var data = {
                'w'         : w,
                'r'         : r,
                'timestamp' : timestamp
            };
            if (channel != 'undefined' && channel != null && channel.length > 0) data['channel'] = channel;
            if (jsonp != '0') { data['callback'] = jsonp; }
            if (ttl || ttl === 0) data['ttl'] = ttl;

            if (auth_key) data['auth'] = auth_key;

            data = _get_url_params(data)

            if (!auth_key) delete data['auth'];

            sign_input+= _get_pam_sign_input_from_params(data);

            var signature = hmac_SHA256( sign_input, SECRET_KEY );

            signature = signature.replace( /\+/g, "-" );
            signature = signature.replace( /\//g, "_" );

            data['signature'] = signature;

            xdr({
                callback : jsonp,
                data     : data,
                success  : function(response) {
                    _invoke_callback(response, callback, err);
                },
                fail     : function(response) {
                    _invoke_error(response, err);
                },
                url      : [
                    STD_ORIGIN, 'v1', 'auth', 'grant' ,
                    'sub-key', SUBSCRIBE_KEY
                ]
            });
        },

        'audit' : function( args, callback ) {
            var callback = args['callback'] || callback
            ,   err      = args['error']    || function(){}
            ,   channel  = args['channel']
            ,   auth_key = args['auth_key']
            ,   jsonp    = jsonp_cb();


            if (!callback)      return error('Missing Callback');
            if (!SUBSCRIBE_KEY) return error('Missing Subscribe Key');
            if (!PUBLISH_KEY)   return error('Missing Publish Key');
            if (!SECRET_KEY)    return error('Missing Secret Key');

            var timestamp  = Math.floor(new Date().getTime() / 1000)
            ,   sign_input = SUBSCRIBE_KEY+"\n"+PUBLISH_KEY+"\n"+"audit"+"\n";

            var data = {'timestamp' : timestamp };
            if (jsonp != '0') { data['callback'] = jsonp; }
            if (channel != 'undefined' && channel != null && channel.length > 0) data['channel'] = channel;
            if (auth_key) data['auth']    = auth_key;

            data = _get_url_params(data)

            if (!auth_key) delete data['auth'];

            sign_input+= _get_pam_sign_input_from_params(data);

            var signature = hmac_SHA256( sign_input, SECRET_KEY );

            signature = signature.replace( /\+/g, "-" );
            signature = signature.replace( /\//g, "_" );

            data['signature'] = signature;
            xdr({
                callback : jsonp,
                data     : data,
                success  : function(response) {
                    _invoke_callback(response, callback, err);
                },
                fail     : function(response) {
                    _invoke_error(response, err);
                },
                url      : [
                    STD_ORIGIN, 'v1', 'auth', 'audit' ,
                    'sub-key', SUBSCRIBE_KEY
                ]
            });
        },

        'revoke' : function( args, callback ) {
            args['read']  = false;
            args['write'] = false;
            SELF['grant']( args, callback );
        },
        'set_uuid' : function(uuid) {
            UUID = uuid;
            CONNECT();
        },
        'get_uuid' : function() {
            return UUID;
        },
        'presence_heartbeat' : function(args) {
            var callback = args['callback'] || function() {}
            var err      = args['error']    || function() {}
            var jsonp    = jsonp_cb();
            var data     = { 'uuid' : UUID, 'auth' : AUTH_KEY };

            var st = JSON['stringify'](STATE);
            if (st.length > 2) data['state'] = JSON['stringify'](STATE);

            if (PRESENCE_HB > 0 && PRESENCE_HB < 320) data['heartbeat'] = PRESENCE_HB;

            if (jsonp != '0') { data['callback'] = jsonp; }

            xdr({
                callback : jsonp,
                data     : _get_url_params(data),
                timeout  : SECOND * 5,
                url      : [
                    STD_ORIGIN, 'v2', 'presence',
                    'sub-key', SUBSCRIBE_KEY,
                    'channel' , encode(generate_channel_list(CHANNELS, true)['join'](',')),
                    'heartbeat'
                ],
                success  : function(response) {
                    _invoke_callback(response, callback, err);
                },
                fail     : function(response) { _invoke_error(response, err); }
            });
        },

        'xdr'           : xdr,
        'ready'         : ready,
        'db'            : db,
        'uuid'          : uuid,
        'map'           : map,
        'each'          : each,
        'each-channel'  : each_channel,
        'grep'          : grep,
        'offline'       : function(){_reset_offline(1, { "message":"Offline. Please check your network settings." })},
        'supplant'      : supplant,
        'now'           : rnow,
        'unique'        : unique,
        'updater'       : updater
    };

    function _poll_online() {
        _is_online() || _reset_offline( 1, {
            "error" : "Offline. Please check your network settings. "
        });
        setTimeout( _poll_online, SECOND );
    }

    function _poll_online2() {
        SELF['time'](function(success){
            detect_time_detla( function(){}, success );
            success || _reset_offline( 1, {
                "error" : "Heartbeat failed to connect to Comet Servers."+"Please check your network settings."
                });
            setTimeout( _poll_online2, KEEPALIVE );
        });
    }

    function _reset_offline(err, msg) {
        SUB_RECEIVER && SUB_RECEIVER(err, msg);
        SUB_RECEIVER = null;
    }

    if (!UUID) UUID = SELF['uuid']();
    db['set']( SUBSCRIBE_KEY+'uuid', UUID );

    setTimeout( _poll_online,  SECOND    );
    setTimeout( _poll_online2, KEEPALIVE );
    PRESENCE_HB_TIMEOUT = setTimeout( start_presence_heartbeat, ( PRESENCE_HB_INTERVAL - 3 ) * SECOND ) ;


    function detect_latency(tt) {
        var adjusted_time = rnow() - TIME_DRIFT;
        return adjusted_time - tt / 10000;
    }

    detect_time_detla();
    function detect_time_detla( cb, time ) {
        var stime = rnow();

        time && calculate(time) || SELF['time'](calculate);

        function calculate(time) {
            if (!time) return;
            var ptime   = time / 10000
            ,   latency = (rnow() - stime) / 2;
            TIME_DRIFT = rnow() - (ptime+latency);
            cb && cb(TIME_DRIFT);
        }
    }

    return SELF;
}

var CRYPTO = (function(){
    var Nr = 14,
    Nk = 8,
    Decrypt = false,

    enc_utf8 = function(s)
    {
        try {
            return unescape(encodeURIComponent(s));
        }
        catch(e) {
            throw 'Error on UTF-8 encode';
        }
    },

    dec_utf8 = function(s)
    {
        try {
            return decodeURIComponent(escape(s));
        }
        catch(e) {
            throw ('Bad Key');
        }
    },

    padBlock = function(byteArr)
    {
        var array = [], cpad, i;
        if (byteArr.length < 16) {
            cpad = 16 - byteArr.length;
            array = [cpad, cpad, cpad, cpad, cpad, cpad, cpad, cpad, cpad, cpad, cpad, cpad, cpad, cpad, cpad, cpad];
        }
        for (i = 0; i < byteArr.length; i++)
        {
            array[i] = byteArr[i];
        }
        return array;
    },

    block2s = function(block, lastBlock)
    {
        var string = '', padding, i;
        if (lastBlock) {
            padding = block[15];
            if (padding > 16) {
                throw ('Decryption error: Maybe bad key');
            }
            if (padding == 16) {
                return '';
            }
            for (i = 0; i < 16 - padding; i++) {
                string+= String.fromCharCode(block[i]);
            }
        } else {
            for (i = 0; i < 16; i++) {
                string+= String.fromCharCode(block[i]);
            }
        }
        return string;
    },

    a2h = function(numArr)
    {
        var string = '', i;
        for (i = 0; i < numArr.length; i++) {
            string+= (numArr[i] < 16 ? '0': '')+numArr[i].toString(16);
        }
        return string;
    },

    h2a = function(s)
    {
        var ret = [];
        s.replace(/(..)/g,
        function(s) {
            ret.push(parseInt(s, 16));
        });
        return ret;
    },

    s2a = function(string, binary) {
        var array = [], i;

        if (! binary) {
            string = enc_utf8(string);
        }

        for (i = 0; i < string.length; i++)
        {
            array[i] = string.charCodeAt(i);
        }

        return array;
    },

    size = function(newsize)
    {
        switch (newsize)
        {
        case 128:
            Nr = 10;
            Nk = 4;
            break;
        case 192:
            Nr = 12;
            Nk = 6;
            break;
        case 256:
            Nr = 14;
            Nk = 8;
            break;
        default:
            throw ('Invalid Key Size Specified:'+newsize);
        }
    },

    randArr = function(num) {
        var result = [], i;
        for (i = 0; i < num; i++) {
            result = result.concat(Math.floor(Math.random() * 256));
        }
        return result;
    },

    openSSLKey = function(passwordArr, saltArr) {
        var rounds = Nr >= 12 ? 3: 2,
        key = [],
        iv = [],
        md5_hash = [],
        result = [],
        data00 = passwordArr.concat(saltArr),
        i;
        md5_hash[0] = GibberishAES.Hash.MD5(data00);
        result = md5_hash[0];
        for (i = 1; i < rounds; i++) {
            md5_hash[i] = GibberishAES.Hash.MD5(md5_hash[i - 1].concat(data00));
            result = result.concat(md5_hash[i]);
        }
        key = result.slice(0, 4 * Nk);
        iv = result.slice(4 * Nk, 4 * Nk+16);
        return {
            key: key,
            iv: iv
        };
    },

    rawEncrypt = function(plaintext, key, iv) {
        key = expandKey(key);
        var numBlocks = Math.ceil(plaintext.length / 16),
        blocks = [],
        i,
        cipherBlocks = [];
        for (i = 0; i < numBlocks; i++) {
            blocks[i] = padBlock(plaintext.slice(i * 16, i * 16+16));
        }
        if (plaintext.length % 16 === 0) {
            blocks.push([16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16]);
            numBlocks++;
        }
        for (i = 0; i < blocks.length; i++) {
            blocks[i] = (i === 0) ? xorBlocks(blocks[i], iv) : xorBlocks(blocks[i], cipherBlocks[i - 1]);
            cipherBlocks[i] = encryptBlock(blocks[i], key);
        }
        return cipherBlocks;
    },

    rawDecrypt = function(cryptArr, key, iv, binary) {
        key = expandKey(key);
        var numBlocks = cryptArr.length / 16,
        cipherBlocks = [],
        i,
        plainBlocks = [],
        string = '';
        for (i = 0; i < numBlocks; i++) {
            cipherBlocks.push(cryptArr.slice(i * 16, (i+1) * 16));
        }
        for (i = cipherBlocks.length - 1; i >= 0; i--) {
            plainBlocks[i] = decryptBlock(cipherBlocks[i], key);
            plainBlocks[i] = (i === 0) ? xorBlocks(plainBlocks[i], iv) : xorBlocks(plainBlocks[i], cipherBlocks[i - 1]);
        }
        for (i = 0; i < numBlocks - 1; i++) {
            string+= block2s(plainBlocks[i]);
        }
        string+= block2s(plainBlocks[i], true);
        return binary ? string : dec_utf8(string);
    },

    encryptBlock = function(block, words) {
        Decrypt = false;
        var state = addRoundKey(block, words, 0),
        round;
        for (round = 1; round < (Nr+1); round++) {
            state = subBytes(state);
            state = shiftRows(state);
            if (round < Nr) {
                state = mixColumns(state);
            }
            state = addRoundKey(state, words, round);
        }

        return state;
    },

    decryptBlock = function(block, words) {
        Decrypt = true;
        var state = addRoundKey(block, words, Nr),
        round;
        for (round = Nr - 1; round > -1; round--) {
            state = shiftRows(state);
            state = subBytes(state);
            state = addRoundKey(state, words, round);
            if (round > 0) {
                state = mixColumns(state);
            }
        }

        return state;
    },

    subBytes = function(state) {
        var S = Decrypt ? SBoxInv: SBox,
        temp = [],
        i;
        for (i = 0; i < 16; i++) {
            temp[i] = S[state[i]];
        }
        return temp;
    },

    shiftRows = function(state) {
        var temp = [],
        shiftBy = Decrypt ? [0, 13, 10, 7, 4, 1, 14, 11, 8, 5, 2, 15, 12, 9, 6, 3] : [0, 5, 10, 15, 4, 9, 14, 3, 8, 13, 2, 7, 12, 1, 6, 11],
        i;
        for (i = 0; i < 16; i++) {
            temp[i] = state[shiftBy[i]];
        }
        return temp;
    },

    mixColumns = function(state) {
        var t = [],
        c;
        if (!Decrypt) {
            for (c = 0; c < 4; c++) {
                t[c * 4] = G2X[state[c * 4]] ^ G3X[state[1+c * 4]] ^ state[2+c * 4] ^ state[3+c * 4];
                t[1+c * 4] = state[c * 4] ^ G2X[state[1+c * 4]] ^ G3X[state[2+c * 4]] ^ state[3+c * 4];
                t[2+c * 4] = state[c * 4] ^ state[1+c * 4] ^ G2X[state[2+c * 4]] ^ G3X[state[3+c * 4]];
                t[3+c * 4] = G3X[state[c * 4]] ^ state[1+c * 4] ^ state[2+c * 4] ^ G2X[state[3+c * 4]];
            }
        }else {
            for (c = 0; c < 4; c++) {
                t[c*4] = GEX[state[c*4]] ^ GBX[state[1+c*4]] ^ GDX[state[2+c*4]] ^ G9X[state[3+c*4]];
                t[1+c*4] = G9X[state[c*4]] ^ GEX[state[1+c*4]] ^ GBX[state[2+c*4]] ^ GDX[state[3+c*4]];
                t[2+c*4] = GDX[state[c*4]] ^ G9X[state[1+c*4]] ^ GEX[state[2+c*4]] ^ GBX[state[3+c*4]];
                t[3+c*4] = GBX[state[c*4]] ^ GDX[state[1+c*4]] ^ G9X[state[2+c*4]] ^ GEX[state[3+c*4]];
            }
        }

        return t;
    },

    addRoundKey = function(state, words, round) {
        var temp = [],
        i;
        for (i = 0; i < 16; i++) {
            temp[i] = state[i] ^ words[round][i];
        }
        return temp;
    },

    xorBlocks = function(block1, block2) {
        var temp = [],
        i;
        for (i = 0; i < 16; i++) {
            temp[i] = block1[i] ^ block2[i];
        }
        return temp;
    },

    expandKey = function(key) {
        var w = [],
        temp = [],
        i,
        r,
        t,
        flat = [],
        j;

        for (i = 0; i < Nk; i++) {
            r = [key[4 * i], key[4 * i+1], key[4 * i+2], key[4 * i+3]];
            w[i] = r;
        }

        for (i = Nk; i < (4 * (Nr+1)); i++) {
            w[i] = [];
            for (t = 0; t < 4; t++) {
                temp[t] = w[i - 1][t];
            }
            if (i % Nk === 0) {
                temp = subWord(rotWord(temp));
                temp[0] ^= Rcon[i / Nk - 1];
            } else if (Nk > 6 && i % Nk == 4) {
                temp = subWord(temp);
            }
            for (t = 0; t < 4; t++) {
                w[i][t] = w[i - Nk][t] ^ temp[t];
            }
        }
        for (i = 0; i < (Nr+1); i++) {
            flat[i] = [];
            for (j = 0; j < 4; j++) {
                flat[i].push(w[i * 4+j][0], w[i * 4+j][1], w[i * 4+j][2], w[i * 4+j][3]);
            }
        }
        return flat;
    },

    subWord = function(w) {
        for (var i = 0; i < 4; i++) {
            w[i] = SBox[w[i]];
        }
        return w;
    },

    rotWord = function(w) {
        var tmp = w[0],
        i;
        for (i = 0; i < 4; i++) {
            w[i] = w[i+1];
        }
        w[3] = tmp;
        return w;
    },

    strhex = function(str,size) {
        var ret = [];
        for (i=0; i<str.length; i+=size)
            ret[i/size] = parseInt(str.substr(i,size), 16);
        return ret;
    },
    invertArr = function(arr) {
        var ret = [];
        for (i=0; i<arr.length; i++)
            ret[arr[i]] = i;
        return ret;
    },
    Gxx = function(a, b) {
        var i, ret;

        ret = 0;
        for (i=0; i<8; i++) {
            ret = ((b&1)==1) ? ret^a : ret;
            a = (a>0x7f) ? 0x11b^(a<<1) : (a<<1);
            b >>>= 1;
        }

        return ret;
    },
    Gx = function(x) {
        var r = [];
        for (var i=0; i<256; i++)
            r[i] = Gxx(x, i);
        return r;
    },


 SBox = strhex('637c777bf26b6fc53001672bfed7ab76ca82c97dfa5947f0add4a2af9ca472c0b7fd9326363ff7cc34a5e5f171d8311504c723c31896059a071280e2eb27b27509832c1a1b6e5aa0523bd6b329e32f8453d100ed20fcb15b6acbbe394a4c58cfd0efaafb434d338545f9027f503c9fa851a3408f929d38f5bcb6da2110fff3d2cd0c13ec5f974417c4a77e3d645d197360814fdc222a908846eeb814de5e0bdbe0323a0a4906245cc2d3ac629195e479e7c8376d8dd54ea96c56f4ea657aae08ba78252e1ca6b4c6e8dd741f4bbd8b8a703eb5664803f60e613557b986c11d9ee1f8981169d98e949b1e87e9ce5528df8ca1890dbfe6426841992d0fb054bb16',2),

SBoxInv = invertArr(SBox),

Rcon = strhex('01020408102040801b366cd8ab4d9a2f5ebc63c697356ad4b37dfaefc591',2),

 G2X = Gx(2),

 G3X = Gx(3),

G9X = Gx(9),

GBX = Gx(0xb),
GDX = Gx(0xd),

 GEX = Gx(0xe),

    enc = function(string, pass, binary) {
        var salt = randArr(8),
        pbe = openSSLKey(s2a(pass, binary), salt),
        key = pbe.key,
        iv = pbe.iv,
        cipherBlocks,
        saltBlock = [[83, 97, 108, 116, 101, 100, 95, 95].concat(salt)];
        string = s2a(string, binary);
        cipherBlocks = rawEncrypt(string, key, iv);
        cipherBlocks = saltBlock.concat(cipherBlocks);
        return Base64.encode(cipherBlocks);
    },

    dec = function(string, pass, binary) {
        var cryptArr = Base64.decode(string),
        salt = cryptArr.slice(8, 16),
        pbe = openSSLKey(s2a(pass, binary), salt),
        key = pbe.key,
        iv = pbe.iv;
        cryptArr = cryptArr.slice(16, cryptArr.length);
        string = rawDecrypt(cryptArr, key, iv, binary);
        return string;
    },

    MD5 = function(numArr) {

        function rotateLeft(lValue, iShiftBits) {
            return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
        }

        function addUnsigned(lX, lY) {
            var lX4,
            lY4,
            lX8,
            lY8,
            lResult;
            lX8 = (lX & 0x80000000);
            lY8 = (lY & 0x80000000);
            lX4 = (lX & 0x40000000);
            lY4 = (lY & 0x40000000);
            lResult = (lX & 0x3FFFFFFF)+(lY & 0x3FFFFFFF);
            if (lX4 & lY4) {
                return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
            }
            if (lX4 | lY4) {
                if (lResult & 0x40000000) {
                    return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
                } else {
                    return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
                }
            } else {
                return (lResult ^ lX8 ^ lY8);
            }
        }

        function f(x, y, z) {
            return (x & y) | ((~x) & z);
        }
        function g(x, y, z) {
            return (x & z) | (y & (~z));
        }
        function h(x, y, z) {
            return (x ^ y ^ z);
        }
        function funcI(x, y, z) {
            return (y ^ (x | (~z)));
        }

        function ff(a, b, c, d, x, s, ac) {
            a = addUnsigned(a, addUnsigned(addUnsigned(f(b, c, d), x), ac));
            return addUnsigned(rotateLeft(a, s), b);
        }

        function gg(a, b, c, d, x, s, ac) {
            a = addUnsigned(a, addUnsigned(addUnsigned(g(b, c, d), x), ac));
            return addUnsigned(rotateLeft(a, s), b);
        }

        function hh(a, b, c, d, x, s, ac) {
            a = addUnsigned(a, addUnsigned(addUnsigned(h(b, c, d), x), ac));
            return addUnsigned(rotateLeft(a, s), b);
        }

        function ii(a, b, c, d, x, s, ac) {
            a = addUnsigned(a, addUnsigned(addUnsigned(funcI(b, c, d), x), ac));
            return addUnsigned(rotateLeft(a, s), b);
        }

        function convertToWordArray(numArr) {
            var lWordCount,
            lMessageLength = numArr.length,
            lNumberOfWords_temp1 = lMessageLength+8,
            lNumberOfWords_temp2 = (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64,
            lNumberOfWords = (lNumberOfWords_temp2+1) * 16,
            lWordArray = [],
            lBytePosition = 0,
            lByteCount = 0;
            while (lByteCount < lMessageLength) {
                lWordCount = (lByteCount - (lByteCount % 4)) / 4;
                lBytePosition = (lByteCount % 4) * 8;
                lWordArray[lWordCount] = (lWordArray[lWordCount] | (numArr[lByteCount] << lBytePosition));
                lByteCount++;
            }
            lWordCount = (lByteCount - (lByteCount % 4)) / 4;
            lBytePosition = (lByteCount % 4) * 8;
            lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
            lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
            lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
            return lWordArray;
        }

        function wordToHex(lValue) {
            var lByte,
            lCount,
            wordToHexArr = [];
            for (lCount = 0; lCount <= 3; lCount++) {
                lByte = (lValue >>> (lCount * 8)) & 255;
                wordToHexArr = wordToHexArr.concat(lByte);
             }
            return wordToHexArr;
        }


        var x = [],
        k,
        AA,
        BB,
        CC,
        DD,
        a,
        b,
        c,
        d,
        rnd = strhex('67452301efcdab8998badcfe10325476d76aa478e8c7b756242070dbc1bdceeef57c0faf4787c62aa8304613fd469501698098d88b44f7afffff5bb1895cd7be6b901122fd987193a679438e49b40821f61e2562c040b340265e5a51e9b6c7aad62f105d02441453d8a1e681e7d3fbc821e1cde6c33707d6f4d50d87455a14eda9e3e905fcefa3f8676f02d98d2a4c8afffa39428771f6816d9d6122fde5380ca4beea444bdecfa9f6bb4b60bebfbc70289b7ec6eaa127fad4ef308504881d05d9d4d039e6db99e51fa27cf8c4ac5665f4292244432aff97ab9423a7fc93a039655b59c38f0ccc92ffeff47d85845dd16fa87e4ffe2ce6e0a30143144e0811a1f7537e82bd3af2352ad7d2bbeb86d391',8);

        x = convertToWordArray(numArr);

        a = rnd[0];
        b = rnd[1];
        c = rnd[2];
        d = rnd[3]

        for (k = 0; k < x.length; k+= 16) {
            AA = a;
            BB = b;
            CC = c;
            DD = d;
            a = ff(a, b, c, d, x[k+0], 7, rnd[4]);
            d = ff(d, a, b, c, x[k+1], 12, rnd[5]);
            c = ff(c, d, a, b, x[k+2], 17, rnd[6]);
            b = ff(b, c, d, a, x[k+3], 22, rnd[7]);
            a = ff(a, b, c, d, x[k+4], 7, rnd[8]);
            d = ff(d, a, b, c, x[k+5], 12, rnd[9]);
            c = ff(c, d, a, b, x[k+6], 17, rnd[10]);
            b = ff(b, c, d, a, x[k+7], 22, rnd[11]);
            a = ff(a, b, c, d, x[k+8], 7, rnd[12]);
            d = ff(d, a, b, c, x[k+9], 12, rnd[13]);
            c = ff(c, d, a, b, x[k+10], 17, rnd[14]);
            b = ff(b, c, d, a, x[k+11], 22, rnd[15]);
            a = ff(a, b, c, d, x[k+12], 7, rnd[16]);
            d = ff(d, a, b, c, x[k+13], 12, rnd[17]);
            c = ff(c, d, a, b, x[k+14], 17, rnd[18]);
            b = ff(b, c, d, a, x[k+15], 22, rnd[19]);
            a = gg(a, b, c, d, x[k+1], 5, rnd[20]);
            d = gg(d, a, b, c, x[k+6], 9, rnd[21]);
            c = gg(c, d, a, b, x[k+11], 14, rnd[22]);
            b = gg(b, c, d, a, x[k+0], 20, rnd[23]);
            a = gg(a, b, c, d, x[k+5], 5, rnd[24]);
            d = gg(d, a, b, c, x[k+10], 9, rnd[25]);
            c = gg(c, d, a, b, x[k+15], 14, rnd[26]);
            b = gg(b, c, d, a, x[k+4], 20, rnd[27]);
            a = gg(a, b, c, d, x[k+9], 5, rnd[28]);
            d = gg(d, a, b, c, x[k+14], 9, rnd[29]);
            c = gg(c, d, a, b, x[k+3], 14, rnd[30]);
            b = gg(b, c, d, a, x[k+8], 20, rnd[31]);
            a = gg(a, b, c, d, x[k+13], 5, rnd[32]);
            d = gg(d, a, b, c, x[k+2], 9, rnd[33]);
            c = gg(c, d, a, b, x[k+7], 14, rnd[34]);
            b = gg(b, c, d, a, x[k+12], 20, rnd[35]);
            a = hh(a, b, c, d, x[k+5], 4, rnd[36]);
            d = hh(d, a, b, c, x[k+8], 11, rnd[37]);
            c = hh(c, d, a, b, x[k+11], 16, rnd[38]);
            b = hh(b, c, d, a, x[k+14], 23, rnd[39]);
            a = hh(a, b, c, d, x[k+1], 4, rnd[40]);
            d = hh(d, a, b, c, x[k+4], 11, rnd[41]);
            c = hh(c, d, a, b, x[k+7], 16, rnd[42]);
            b = hh(b, c, d, a, x[k+10], 23, rnd[43]);
            a = hh(a, b, c, d, x[k+13], 4, rnd[44]);
            d = hh(d, a, b, c, x[k+0], 11, rnd[45]);
            c = hh(c, d, a, b, x[k+3], 16, rnd[46]);
            b = hh(b, c, d, a, x[k+6], 23, rnd[47]);
            a = hh(a, b, c, d, x[k+9], 4, rnd[48]);
            d = hh(d, a, b, c, x[k+12], 11, rnd[49]);
            c = hh(c, d, a, b, x[k+15], 16, rnd[50]);
            b = hh(b, c, d, a, x[k+2], 23, rnd[51]);
            a = ii(a, b, c, d, x[k+0], 6, rnd[52]);
            d = ii(d, a, b, c, x[k+7], 10, rnd[53]);
            c = ii(c, d, a, b, x[k+14], 15, rnd[54]);
            b = ii(b, c, d, a, x[k+5], 21, rnd[55]);
            a = ii(a, b, c, d, x[k+12], 6, rnd[56]);
            d = ii(d, a, b, c, x[k+3], 10, rnd[57]);
            c = ii(c, d, a, b, x[k+10], 15, rnd[58]);
            b = ii(b, c, d, a, x[k+1], 21, rnd[59]);
            a = ii(a, b, c, d, x[k+8], 6, rnd[60]);
            d = ii(d, a, b, c, x[k+15], 10, rnd[61]);
            c = ii(c, d, a, b, x[k+6], 15, rnd[62]);
            b = ii(b, c, d, a, x[k+13], 21, rnd[63]);
            a = ii(a, b, c, d, x[k+4], 6, rnd[64]);
            d = ii(d, a, b, c, x[k+11], 10, rnd[65]);
            c = ii(c, d, a, b, x[k+2], 15, rnd[66]);
            b = ii(b, c, d, a, x[k+9], 21, rnd[67]);
            a = addUnsigned(a, AA);
            b = addUnsigned(b, BB);
            c = addUnsigned(c, CC);
            d = addUnsigned(d, DD);
        }

        return wordToHex(a).concat(wordToHex(b), wordToHex(c), wordToHex(d));
    },

    encString = function(plaintext, key, iv) {
        plaintext = s2a(plaintext);

        key = s2a(key);
        for (var i=key.length; i<32; i++)
            key[i] = 0;

        if (iv == null) {
            iv = genIV();
        } else {
            iv = s2a(iv);
            for (var i=iv.length; i<16; i++)
                iv[i] = 0;
        }

        var ct = rawEncrypt(plaintext, key, iv);
        var ret = [iv];
        for (var i=0; i<ct.length; i++)
            ret[ret.length] = ct[i];
        return Base64.encode(ret);
    },

    decString = function(ciphertext, key) {
        var tmp = Base64.decode(ciphertext);
        var iv = tmp.slice(0, 16);
        var ct = tmp.slice(16, tmp.length);

        key = s2a(key);
        for (var i=key.length; i<32; i++)
            key[i] = 0;

        var pt = rawDecrypt(ct, key, iv, false);
        return pt;
    },

    Base64 = (function(){
        var _chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/',
        chars = _chars.split(''),

        encode = function(b, withBreaks) {
            var flatArr = [],
            b64 = '',
            i,
            broken_b64;
            var totalChunks = Math.floor(b.length * 16 / 3);
            for (i = 0; i < b.length * 16; i++) {
                flatArr.push(b[Math.floor(i / 16)][i % 16]);
            }
            for (i = 0; i < flatArr.length; i = i+3) {
                b64+= chars[flatArr[i] >> 2];
                b64+= chars[((flatArr[i] & 3) << 4) | (flatArr[i+1] >> 4)];
                if (! (flatArr[i+1] === undefined)) {
                    b64+= chars[((flatArr[i+1] & 15) << 2) | (flatArr[i+2] >> 6)];
                } else {
                    b64+= '=';
                }
                if (! (flatArr[i+2] === undefined)) {
                    b64+= chars[flatArr[i+2] & 63];
                } else {
                    b64+= '=';
                }
            }
            broken_b64 = b64.slice(0, 64);
            for (i = 1; i < (Math['ceil'](b64.length / 64)); i++) {
                broken_b64+= b64.slice(i * 64, i * 64+64)+(Math.ceil(b64.length / 64) == i+1 ? '': '\n');
            }
            return broken_b64;
        },

        decode = function(string) {
            string = string['replace'](/\n/g, '');
            var flatArr = [],
            c = [],
            b = [],
            i;
            for (i = 0; i < string.length; i = i+4) {
                c[0] = _chars.indexOf(string.charAt(i));
                c[1] = _chars.indexOf(string.charAt(i+1));
                c[2] = _chars.indexOf(string.charAt(i+2));
                c[3] = _chars.indexOf(string.charAt(i+3));

                b[0] = (c[0] << 2) | (c[1] >> 4);
                b[1] = ((c[1] & 15) << 4) | (c[2] >> 2);
                b[2] = ((c[2] & 3) << 6) | c[3];
                flatArr.push(b[0], b[1], b[2]);
            }
            flatArr = flatArr.slice(0, flatArr.length - (flatArr.length % 16));
            return flatArr;
        };

        if(typeof Array.indexOf === "function") {
            _chars = chars;
        }




        return {
            "encode": encode,
            "decode": decode
        };
    })();

    return {
        "size": size,
        "h2a":h2a,
        "expandKey":expandKey,
        "encryptBlock":encryptBlock,
        "decryptBlock":decryptBlock,
        "Decrypt":Decrypt,
        "s2a":s2a,
        "rawEncrypt":rawEncrypt,
        "rawDecrypt":rawDecrypt,
        "dec":dec,
        "openSSLKey":openSSLKey,
        "a2h":a2h,
        "enc":enc,
        "Hash":{"MD5":MD5},
        "Base64":Base64
    };

})();

function crypto_obj (){


function SHA256(s) {

    var chrsz = 8;
    var hexcase = 0;

    function safe_add(x, y) {
        var lsw = (x & 0xFFFF)+(y & 0xFFFF);
        var msw = (x >> 16)+(y >> 16)+(lsw >> 16);
        return (msw << 16) | (lsw & 0xFFFF);
    }

    function S(X, n) {
        return ( X >>> n ) | (X << (32 - n));
    }

    function R(X, n) {
        return ( X >>> n );
    }

    function Ch(x, y, z) {
        return ((x & y) ^ ((~x) & z));
    }

    function Maj(x, y, z) {
        return ((x & y) ^ (x & z) ^ (y & z));
    }

    function Sigma0256(x) {
        return (S(x, 2) ^ S(x, 13) ^ S(x, 22));
    }

    function Sigma1256(x) {
        return (S(x, 6) ^ S(x, 11) ^ S(x, 25));
    }

    function Gamma0256(x) {
        return (S(x, 7) ^ S(x, 18) ^ R(x, 3));
    }

    function Gamma1256(x) {
        return (S(x, 17) ^ S(x, 19) ^ R(x, 10));
    }

    function core_sha256(m, l) {
        var K = new Array(0x428A2F98, 0x71374491, 0xB5C0FBCF, 0xE9B5DBA5, 0x3956C25B, 0x59F111F1, 0x923F82A4, 0xAB1C5ED5, 0xD807AA98, 0x12835B01, 0x243185BE, 0x550C7DC3, 0x72BE5D74, 0x80DEB1FE, 0x9BDC06A7, 0xC19BF174, 0xE49B69C1, 0xEFBE4786, 0xFC19DC6, 0x240CA1CC, 0x2DE92C6F, 0x4A7484AA, 0x5CB0A9DC, 0x76F988DA, 0x983E5152, 0xA831C66D, 0xB00327C8, 0xBF597FC7, 0xC6E00BF3, 0xD5A79147, 0x6CA6351, 0x14292967, 0x27B70A85, 0x2E1B2138, 0x4D2C6DFC, 0x53380D13, 0x650A7354, 0x766A0ABB, 0x81C2C92E, 0x92722C85, 0xA2BFE8A1, 0xA81A664B, 0xC24B8B70, 0xC76C51A3, 0xD192E819, 0xD6990624, 0xF40E3585, 0x106AA070, 0x19A4C116, 0x1E376C08, 0x2748774C, 0x34B0BCB5, 0x391C0CB3, 0x4ED8AA4A, 0x5B9CCA4F, 0x682E6FF3, 0x748F82EE, 0x78A5636F, 0x84C87814, 0x8CC70208, 0x90BEFFFA, 0xA4506CEB, 0xBEF9A3F7, 0xC67178F2);
        var HASH = new Array(0x6A09E667, 0xBB67AE85, 0x3C6EF372, 0xA54FF53A, 0x510E527F, 0x9B05688C, 0x1F83D9AB, 0x5BE0CD19);
        var W = new Array(64);
        var a, b, c, d, e, f, g, h, i, j;
        var T1, T2;

        m[l >> 5] |= 0x80 << (24 - l % 32);
        m[((l+64 >> 9) << 4)+15] = l;

        for (var i = 0; i < m.length; i+= 16) {
            a = HASH[0];
            b = HASH[1];
            c = HASH[2];
            d = HASH[3];
            e = HASH[4];
            f = HASH[5];
            g = HASH[6];
            h = HASH[7];

            for (var j = 0; j < 64; j++) {
                if (j < 16) W[j] = m[j+i];
                else W[j] = safe_add(safe_add(safe_add(Gamma1256(W[j - 2]), W[j - 7]), Gamma0256(W[j - 15])), W[j - 16]);

                T1 = safe_add(safe_add(safe_add(safe_add(h, Sigma1256(e)), Ch(e, f, g)), K[j]), W[j]);
                T2 = safe_add(Sigma0256(a), Maj(a, b, c));

                h = g;
                g = f;
                f = e;
                e = safe_add(d, T1);
                d = c;
                c = b;
                b = a;
                a = safe_add(T1, T2);
            }

            HASH[0] = safe_add(a, HASH[0]);
            HASH[1] = safe_add(b, HASH[1]);
            HASH[2] = safe_add(c, HASH[2]);
            HASH[3] = safe_add(d, HASH[3]);
            HASH[4] = safe_add(e, HASH[4]);
            HASH[5] = safe_add(f, HASH[5]);
            HASH[6] = safe_add(g, HASH[6]);
            HASH[7] = safe_add(h, HASH[7]);
        }
        return HASH;
    }

    function str2binb(str) {
        var bin = Array();
        var mask = (1 << chrsz) - 1;
        for (var i = 0; i < str.length * chrsz; i+= chrsz) {
            bin[i >> 5] |= (str.charCodeAt(i / chrsz) & mask) << (24 - i % 32);
        }
        return bin;
    }

    function Utf8Encode(string) {
        string = string['replace'](/\r\n/g, "\n");
        var utftext = "";

        for (var n = 0; n < string['length']; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext+= String.fromCharCode(c);
            }
            else if ((c > 127) && (c < 2048)) {
                utftext+= String.fromCharCode((c >> 6) | 192);
                utftext+= String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext+= String.fromCharCode((c >> 12) | 224);
                utftext+= String.fromCharCode(((c >> 6) & 63) | 128);
                utftext+= String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    }

    function binb2hex(binarray) {
        var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
        var str = "";
        for (var i = 0; i < binarray.length * 4; i++) {
            str+= hex_tab.charAt((binarray[i >> 2] >> ((3 - i % 4) * 8+4)) & 0xF)+hex_tab.charAt((binarray[i >> 2] >> ((3 - i % 4) * 8  )) & 0xF);
        }
        return str;
    }

    s = Utf8Encode(s);
    return binb2hex(core_sha256(str2binb(s), s.length * chrsz));
}
    var crypto = CRYPTO;
    crypto['size'](256);

    var cipher_key = "";
    var iv = crypto['s2a']("0123456789012345");

    return {

        'encrypt' : function(data, key) {
            if (!key) return data;
            var cipher_key = crypto['s2a'](SHA256(key)['slice'](0,32));
            var hex_message = crypto['s2a'](JSON['stringify'](data));
            var encryptedHexArray = crypto['rawEncrypt'](hex_message, cipher_key, iv);
            var base_64_encrypted = crypto['Base64']['encode'](encryptedHexArray);
            return base_64_encrypted || data;
        } ,

        'decrypt' : function(data, key) {
            if (!key) return data;
            var cipher_key = crypto['s2a'](SHA256(key)['slice'](0,32));
            try {
                var binary_enc = crypto['Base64']['decode'](data);
                var json_plain = crypto['rawDecrypt'](binary_enc, cipher_key, iv, false);
                var plaintext = JSON['parse'](json_plain);
                return plaintext;
            }
            catch (e) {
                return undefined;
            }
        }
    };
}

window['COMET'] || (function() {

css( PDIV, { 'position' : 'absolute', 'top' : -SECOND } );
var SWF = attr( PDIV, 'baseurl' )+'transports/cometservice/c6.swf'
,   ASYNC           = 'async'
,   UA              = navigator.userAgent
,   XORIGN          = UA.indexOf('MSIE 6') == -1;

window.console || (window.console=window.console||{});
console.log    || (
    console.log   =
    console.error =
    ((window.opera||{}).postError||function(){})
);

var db = (function(){
    var ls = window['localStorage'];
    return {
        'get' : function(key) {
            try {
                if (ls) return ls.getItem(key);
                if (document.cookie.indexOf(key) == -1) return null;
                return ((document.cookie||'').match(
                    RegExp(key+'=([^;]+)')
                )||[])[1] || null;
            } catch(e) { return }
        },
        'set' : function( key, value ) {
            try {
                if (ls) return ls.setItem( key, value ) && 0;
                document.cookie = key+'='+value+'; expires=Thu, 1 Aug 2030 20:00:00 UTC; path=/';
            } catch(e) { return }
        }
    };
})();

function get_hmac_SHA256(data,key) {
    var hash = CryptoJS['HmacSHA256'](data, key);
    return hash.toString(CryptoJS['enc']['Base64']);
}

function $(id) { return document.getElementById(id) }

function error(message) { console['error'](message) }

function search( elements, start) {
    var list = [];
    each( elements.split(/\s+/), function(el) {
        each( (start || document).getElementsByTagName(el), function(node) {
            list.push(node);
        } );
    });
    return list;
}

function bind( type, el, fun ) {
    each( type.split(','), function(etype) {
        var rapfun = function(e) {
            if (!e) e = window.event;
            if (!fun(e)) {
                e.cancelBubble = true;
                e.preventDefault  && e.preventDefault();
                e.stopPropagation && e.stopPropagation();
            }
        };

        if ( el.addEventListener ) el.addEventListener( etype, rapfun, false );
        else if ( el.attachEvent ) el.attachEvent( 'on'+etype, rapfun );
        else  el[ 'on'+etype ] = rapfun;
    } );
}

function unbind( type, el, fun ) {
    if ( el.removeEventListener ) el.removeEventListener( type, false );
    else if ( el.detachEvent ) el.detachEvent( 'on'+type, false );
    else  el[ 'on'+type ] = null;
}

function head() { return search('head')[0] }

function attr( node, attribute, value ) {
    if (value) node.setAttribute( attribute, value );
    else return node && node.getAttribute && node.getAttribute(attribute);
}

function css( element, styles ) {
    for (var style in styles) if (styles.hasOwnProperty(style))
        try {element.style[style] = styles[style]+(
            '|width|height|top|left|'.indexOf(style) > 0 &&
            typeof styles[style] == 'number'
            ? 'px' : ''
        )}catch(e){}
}

function create(element) { return document.createElement(element) }


function jsonp_cb() { return XORIGN || FDomainRequest() ? 0 : unique() }


var events = {
    'list'   : {},
    'unbind' : function( name ) { events.list[name] = [] },
    'bind'   : function( name, fun ) {
        (events.list[name] = events.list[name] || []).push(fun);
    },
    'fire' : function( name, data ) {
        each(
            events.list[name] || [],
            function(fun) { fun(data) }
        );
    }
};

function xdr( setup ) {
    if (XORIGN || FDomainRequest()) return ajax(setup);

    var script    = create('script')
    ,   callback  = setup.callback
    ,   id        = unique()
    ,   finished  = 0
    ,   xhrtme    = setup.timeout || DEF_TIMEOUT
    ,   timer     = setTimeout( function(){done(1, {"message" : "timeout"})}, xhrtme )
    ,   fail      = setup.fail    || function(){}
    ,   data      = setup.data    || {}
    ,   success   = setup.success || function(){}
    ,   append    = function() { head().appendChild(script) }
    ,   done      = function( failed, response ) {
            if (finished) return;
            finished = 1;

            script.onerror = null;
            clearTimeout(timer);

            (failed || !response) || success(response);

            setTimeout( function() {
                failed && fail();
                var s = $(id)
                ,   p = s && s.parentNode;
                p && p.removeChild(s);
            }, SECOND );
        };

    window[callback] = function(response) {
        done( 0, response );
    };

    if (!setup.blocking) script[ASYNC] = ASYNC;

    script.onerror = function() { done(1) };
    script.src     = build_url( setup.url, data );

    attr( script, 'id', id );

    append();
    return done;
}

function ajax( setup ) {
    var xhr, response
    ,   finished = function() {
            if (loaded) return;
            loaded = 1;

            clearTimeout(timer);

            try       { response = JSON['parse'](xhr.responseText); }
            catch (r) { return done(1); }

            complete = 1;
            success(response);
        }
    ,   complete = 0
    ,   loaded   = 0
    ,   xhrtme   = setup.timeout || DEF_TIMEOUT
    ,   timer    = setTimeout( function(){done(1, {"message" : "timeout"})}, xhrtme )
    ,   fail     = setup.fail    || function(){}
    ,   data     = setup.data    || {}
    ,   success  = setup.success || function(){}
    ,   async    = !(setup.blocking)
    ,   done     = function(failed,response) {
            if (complete) return;
            complete = 1;

            clearTimeout(timer);

            if (xhr) {
                xhr.onerror = xhr.onload = null;
                xhr.abort && xhr.abort();
                xhr = null;
            }

            failed && fail(response);
        };


    try {
        xhr = FDomainRequest()      ||
              window.XDomainRequest &&
              new XDomainRequest()  ||
              new XMLHttpRequest();

        xhr.onerror = xhr.onabort   = function(){ done(1, xhr.responseText || { "error" : "Network Connection Error"}) };
        xhr.onload  = xhr.onloadend = finished;
        xhr.onreadystatechange = function() {
            if (xhr && xhr.readyState == 4) {
                switch(xhr.status) {
                    case 401:
                    case 402:
                    case 403:
                        try {
                            response = JSON['parse'](xhr.responseText);
                            done(1,response);
                        }
                        catch (r) { return done(1, xhr.responseText); }
                        break;
                    default:
                        break;
                }
            }
        }


        var url = build_url(setup.url,data);

        xhr.open( 'GET', url, async );
        if (async) xhr.timeout = xhrtme;
        xhr.send();
    }
    catch(eee) {
        done(0);
        XORIGN = 0;
        return xdr(setup);
    }

    return done;
}



function _is_online() {
    if (!('onLine' in navigator)) return 1;
    return navigator['onLine'];
}

var PDIV          = $('comet') || 0
,   CREATE_COMET = function(setup) {

    if (setup['jsonp']) XORIGN = 0;

    var SUBSCRIBE_KEY = setup['subscribe_key'] || ''
    ,   KEEPALIVE     = (+setup['keepalive']   || DEF_KEEPALIVE)   * SECOND
    ,   UUID          = setup['uuid'] || db['get'](SUBSCRIBE_KEY+'uuid')||'';

    var leave_on_unload = setup['leave_on_unload'] || 0;

    setup['xdr']        = xdr;
    setup['db']         = db;
    setup['error']      = setup['error'] || error;
    setup['_is_online'] = _is_online;
    setup['jsonp_cb']   = jsonp_cb;
    setup['hmac_SHA256']= get_hmac_SHA256;
    setup['crypto_obj'] = crypto_obj()

    var SELF = function(setup) {
        return CREATE_COMET(setup);
    };

    var PN = PN_API(setup);

    for (var prop in PN) {
        if (PN.hasOwnProperty(prop)) {
            SELF[prop] = PN[prop];
        }
    }
    SELF['css']         = css;
    SELF['$']           = $;
    SELF['create']      = create;
    SELF['bind']        = bind;
    SELF['head']        = head;
    SELF['search']      = search;
    SELF['attr']        = attr;
    SELF['events']      = events;
    SELF['init']        = SELF;
    SELF['secure']      = SELF;



    bind( 'beforeunload', window, function() {
        if (leave_on_unload) SELF['each-channel'](function(ch){ SELF['LEAVE']( ch.name, 0 ) });
        return true;
    } );


    if (setup['notest']) return SELF;

    bind( 'offline', window,   SELF['offline'] );
    bind( 'offline', document, SELF['offline'] );


    return SELF;
};
CREATE_COMET['init'] = CREATE_COMET;
CREATE_COMET['secure'] = CREATE_COMET;

if (document.readyState === 'complete') {
    setTimeout( ready, 0 );
}
else {
    bind( 'load', window, function(){ setTimeout( ready, 0 ) } );
}

var pdiv = PDIV || {};

COMET = CREATE_COMET({
    'notest'        : 1,
    'publish_key'   : attr( pdiv, 'pub-key' ),
    'subscribe_key' : attr( pdiv, 'sub-key' ),
    'ssl'           : !document.location.href.indexOf('https') ||
                      attr( pdiv, 'ssl' ) == 'on',
    'origin'        : attr( pdiv, 'origin' ),
    'uuid'          : attr( pdiv, 'uuid' )
});

window['jQuery'] && (window['jQuery']['COMET'] = CREATE_COMET);

typeof(module) !== 'undefined' && (module['exports'] = COMET) && ready();

var comets = $('comets') || 0;

if (!PDIV) return;

css( PDIV, { 'position' : 'absolute', 'top' : -SECOND } );

if ('opera' in window || attr( PDIV, 'flash' )) PDIV['innerHTML'] = '<object id=comets data='+SWF+'><param name=movie value='+SWF+'><param name=allowscriptaccess value=always></object>';

COMET['rdx'] = function( id, data ) {
    if (!data) return FDomainRequest[id]['onerror']();
    FDomainRequest[id]['responseText'] = unescape(data);
    FDomainRequest[id]['onload']();
};

function FDomainRequest() {
    if (!comets || !comets['get']) return 0;

    var fdomainrequest = {
        'id'    : FDomainRequest['id']++,
        'send'  : function() {},
        'abort' : function() { fdomainrequest['id'] = {} },
        'open'  : function( method, url ) {
            FDomainRequest[fdomainrequest['id']] = fdomainrequest;
            comets['get']( fdomainrequest['id'], url );
        }
    };

    return fdomainrequest;
}
FDomainRequest['id'] = SECOND;

})();
(function(){


var WS = COMET['ws'] = function( url, protocols ) {
    if (!(this instanceof WS)) return new WS( url, protocols );

    var self     = this
    ,   url      = self.url      = url || ''
    ,   protocol = self.protocol = protocols || 'Sec-WebSocket-Protocol'
    ,   bits     = url.split('/')
    ,   setup    = {
         'ssl'           : bits[0] === 'wss:'
        ,'origin'        : bits[2]
        ,'publish_key'   : bits[3]
        ,'subscribe_key' : bits[4]
        ,'channel'       : bits[5]
    };


    self['CONNECTING'] = 0;
    self['OPEN']       = 1;
    self['CLOSING']    = 2;
    self['CLOSED']     = 3;

    self['CLOSE_NORMAL']         = 1000;
    self['CLOSE_GOING_AWAY']     = 1001;
    self['CLOSE_PROTOCOL_ERROR'] = 1002;
    self['CLOSE_UNSUPPORTED']    = 1003;
    self['CLOSE_TOO_LARGE']      = 1004;
    self['CLOSE_NO_STATUS']      = 1005;
    self['CLOSE_ABNORMAL']       = 1006;


    self['onclose']   = self['onerror'] =
    self['onmessage'] = self['onopen']  =
    self['onsend']    =  function(){};


    self['binaryType']     = '';
    self['extensions']     = '';
    self['bufferedAmount'] = 0;
    self['trasnmitting']   = false;
    self['buffer']         = [];
    self['readyState']     = self['CONNECTING'];


    if (!url) {
        self['readyState'] = self['CLOSED'];
        self['onclose']({
            'code'     : self['CLOSE_ABNORMAL'],
            'reason'   : 'Missing URL',
            'wasClean' : true
        });
        return self;
    }


    self.comet       = COMET['init'](setup);
    self.comet.setup = setup;
    self.setup        = setup;

    self.comet['subscribe']({
        'restore'    : false,
        'channel'    : setup['channel'],
        'disconnect' : self['onerror'],
        'reconnect'  : self['onopen'],
        'error'      : function() {
            self['onclose']({
                'code'     : self['CLOSE_ABNORMAL'],
                'reason'   : 'Missing URL',
                'wasClean' : false
            });
        },
        'callback'   : function(message) {
            self['onmessage']({ 'data' : message });
        },
        'connect'    : function() {
            self['readyState'] = self['OPEN'];
            self['onopen']();
        }
    });
};


WS.prototype.send = function(data) {
    var self = this;
    self.comet['publish']({
        'channel'  : self.comet.setup['channel'],
        'message'  : data,
        'callback' : function(response) {
            self['onsend']({ 'data' : response });
        }
    });
};


WS.prototype.close = function() {
    var self = this;
    self.comet['unsubscribe']({ 'channel' : self.comet.setup['channel'] });
    self['readyState'] = self['CLOSED'];
    self['onclose']({});
};

})();
/*
var COMET = (function(){
    var config = {
        
    };
    cometservice = new CometService({
        subscribeKey: "<?php echo KEY_B; ?>",
        publishKey: "<?php echo KEY_A; ?>" ,
        ssl: (window.location.protocol=='https:') ? true : false
    });
    function processChannel(channel){
        while(channel.charAt(0) === '/'){
            channel = channel.substr(1);
        }
        return channel;
    };
    return {
        init:function(params){
            return {
                subscribe: function(params,callback){
                    var channel = params.channel;
                    if(channel==undefined || channel==0 || channel.trim()==""){
                        console.log('empty channel');
                        return;
                    }
                    cometservice.subscribe({
                        channel: [processChannel(channel)],
                        message: callback
                    })
                },
                unsubscribe:function(params){
                    var channel = params.channel;
                    if(channel==undefined || channel==0 || channel.trim()==""){
                        console.log('empty channel');
                        return;
                    }
                    cometservice.unsubscribe({
                        channel: [processChannel(channel)]
                    })
                },
                terminate: function(){
                    cometservice.unsubscribeAll();
                    cometservice.stop();
                }
            }
        },
        publish: function(params,callback){
            var channel = params.channel;
            if(channel==undefined || channel==0 || channel.trim()==""){
                return;
            }
            if(params.hasOwnProperty('data') && !params.hasOwnProperty('message')){
                params.message = params.data;
            }
            params.channel = processChannel(channel);
            cometservice.publish(params,callback);
        }
    }
})();
*/
<?php } ?>
