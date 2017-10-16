<?php
$AliConfig = array (
		//应用ID,您的APPID。
		'app_id' => "2017060907455218",

		//商户私钥
		'merchant_private_key' => "MIIEpAIBAAKCAQEAxnOIZKvvR4EauuxPe8+lMmNTRU5aqith+mQaFg/gR5gBvk1og//CTJlYjuXAgxxgO0M+I/IVgTtCmcqbXgjb7U4gQcyVTpfFLpsfLYz2v8jeiadYbeT4W/IMXcvKmXp1cOxH3OBv4Xltz6vFy4ss1cJ/gQyMMYBIHN4IGXXw8bHCyEYpoDnyNjahbvvrad0RSB9Akvm91MJDQEmufZ2BB9HWiX64zXnAXHIQbXIO93AffqN5KPQHub/4xMwqDvzF+bEb6Vi/yR6aNA78Y0C+kKPEIZlHtInBsLHMhqkrPPkCgKFL8jyHOyXxZx7dEOm+FEwlWpn7ZXpkGI+Y3w2cPwIDAQABAoIBAQDFKU1t71/n432CDnsdX/wZJpM5fRIYlMdf9Anyt0008/FvdwqKchRA8+0G834jBJMa7cCUB9STsyOFFcTsVNLjXkYv+Sixj5mopxb/s1gGzHNDwY3aiKyy9LSSj4C2oPKDAUyYRicBlRmjRF5bzeb6bKUuuh+ioneCrpjPaty50gjVuUowYgOdCdAAhbjN4qzzPd1CGt5x9BpyEvjZ4Fq6hhMYIc1M0br2l6ywgVhk/odT+tnHODXGmvi6N5mD1eWzxsZ5EVZ2Sl+ZpjccrdjFeZb65wNqxKXAwqBm5KCmGUMZSGUPE8ZRiCavbSdlzdoFrXoBw+6RPXfe0l8CcgzxAoGBAOc947npy/OVPbK4SxGSZxMHOWSi/bIdmCt4XqCiOzKxPD+9t4JMx5jgGVk5fMcOSSk/tzMWW+rGqxkUrFyp2bUfKKXrTaNi4IjvGreH8YqQ5YjlaZXMsIdciZ71h3ZN/9DBcPl+YIxVjylneUOqzvr52S1WuULjAa2XcddA50bXAoGBANuy444oTNWUxkGK3V5r9QpWCiSsxVVVj4G+qEML1oqz670zVDotLHbBoBdqgc5N7VdDeqiyQS+kTFVtZrRfKLq33Gwh1LaPi81uTxvnbVyd2lt5NwS+l6v5ayiaXwQszO0Om61WRQXjh+URVB1MHl9FLmmEEJemv+eVTgfuD/DZAoGAdu+SLZFe4U4licLYeZU/hr30exqKOg6WseUbZquKnywhvPcrZ81t6+d3oji7QPbMEnc/FvutEzhT0HadoJuL6mi4U36PVDYLHuM8bqFxTr/wD1VP1UiOk1C5SBUpM2Qy64BTR0AFEKkBFV6vNGqqQtQ3K+arKwfvWQXH+9raGckCgYBRYU5RVjQ/2UAm/x1I4IyAK6bONwFRvsPNt6X0T+pErqjgCKdmdV1HECoRAm7a0Jrd/CzvWDg1QZLVAhVNMwKPR5Pqqg11Im8SxY2gNHWaHQ7JW3k51K+yEE3VWHlhvoaaORMJfi9LIyEvhN+3in6lo6axhy3uPuJPEks5PMHC4QKBgQDF6x7qFR+sraIfy6AINoXcqrqnYrVFE5TQQuuhHqWHOEAzLlPkFSyizgqdTuepLL+Sfucd8wlsbKrCP7nCFEsUX4gdfUvoyNZ/Lr4aPJdqQi1ECywmQsmkg28m5bZxsnd/+/NC2SCj/ioRrVw2I2Ut0mPOWDEnW37QHiso85fFJA==",
		
		//异步通知地址
		'notify_url' => "http://app.7pqun.com/pay/alinotify",
		
		//同步跳转
		'return_url' => "http://app.7pqun.com/person/payResult",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnDsADRzZYjynNILO8pqGYQfpVpXVYWU4nOAlvz7b37gtof3eB96Rmspl4/9Fu+W9kKv3MIM6Vn076akHo9WPGItosMndGNQWW2eFnZ+L/qh4uqm8TAntQLT0ikRXJyTYBdfj64ij+FeDEwGhVz/pjBPyvYaHruHCVoUqLyhqCVC63bZdawIah4uvXBTrmHWK2KAtWZbxgGq5DaDzmph6waRFc4ppHWBI3kHc62vRmfgU24DMETNapM5KAVf3vqlRk6Q96oHTn0MPA+NAf3Tgbp7iISo2lBsXmdojz3tpulcM1s4stsASp93qe9+Qrvjt4GsbF3A73+EpB2UvoGwibwIDAQAB",
);