<?php
$config = array (
		//签名方式,默认为RSA2(RSA2048)
		'sign_type' => "RSA2",

		//支付宝公钥
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuY9CiPMdNBXBx7DdYdEH9bgEmHIl54+xc6qcq8aIiPNgeBoNgmTsmsJs10C7yYR6/qRqjFRKTLn+jsNGMeVmsXsMYP3MaInvwAo2N6Nlq4HATj5TqLfAQfPv3IyXBvOc+qFwCTs2pt/yjkfr/09jzycsTmX5PTehenJCzKdss/ncVIoOWzxm2pdnx2ecBYmdnQs3BwhMaoTWAnhc5fSoLd8Qn4QihjHICsxpFoBeQs3Lg1n/pcqSCIVDnOjuQ57Njo/rpiKx3bHSZewAGI0YjVEUIRJFCmJqfIcNgOrYOWtsIKNKukCwwz0QBrU3NBbYLY1nKizzwlJ4yon6zfvkKQIDAQAB",

		//商户私钥
		'merchant_private_key' => "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCEuyFsDtVCr7vjZEqk3uFQ+Z/SU8HWVqoODkXZXda2tvBcqntIGLOiSbKEjrpMR/MNjrQ3Qombu5V4h8EaSrFR/4o3egp9v37CogbBbMZBVarmZFT3BVVfsLq2lS308ABw2TiHhS5eb6EeXLeyWBk8hqB2A7gkMowyvZRppKMygefhtC/+DxW2o8yrWtPFsZvZt4niMaRzOPmOTs52iAz4U8Mb/2bjNopiKW1qcPtw2KbLmjhDiGzEGMaTwMnlzbx5cBF0w5JzUk6FIk7pWQcGZcr5ri5FF/4k7aCJiHSg3H7uP1kWs65SrcNku9GKGc3x4uVSSGH+zYDm3d1Pvnj9AgMBAAECggEAQ1GBrwOK6CqnGbppHVupXV9tzsgKPMJxt3VHueodF0iLegRBSJy2pu3H6/FTzZKEVHD3ODu3/VNTiPEZYFNXweXgXSi5n3N7QA3bbUjU+JgReI6UhUCgP0ssN6RLzbnYD2QshpdmoCDvz61owbxq+EazpYdXYox8jNOYLUiCOP/r6oIQQjMDE3A082jNpbjW/I6mxOLuEsqsnI2QMAS4ry8y5/Fnw9CcPl6BoXrWO8Fdb3TXQXtHcPxcUgYHIJl/LKV6GnUWgCnC6b7Uw8oI1Gm/pKmpwKE0TUILyn/sAqgvBLzWnNcGQNa9pHL48Q04mqCWXgmaEhzy1+cky6TKxQKBgQDM6B2phgAUnw5/N8HdIOZ+Nj7EwC4fXuG4mHv0StOUE9SMDRZttMLWQipxBFppZ4+nEcTOo+LiwPl0cWzh794X3Km9SumoeeHz/Jrt3eV9lW5LDj1PakUbVVnx4rvgwsoyhQj4kKyC5UvvJnJXiZYk1hEbOb/Q2lqp1XcT35WHlwKBgQCl08uhhfs7TIAE/QHUptKsaVF7DYd/hVnqXAhnogFYnXTa/2F+SjbMH7p4g2m6nMsE6RzjMakuwl/3pIoSWLbryJnCqWJAYP7GUOiOcSmsAOS82G410EHansqA1FGnK+GYRjdv7AKh75S/6LsRPfNFHBZrkR7numj9ZqUklyw2iwKBgQCp6S/virY2Y05aH5oaC1YWAlU6QUH9sWfq8kaW5BVeDLOLDq8yeVm24VbgMIPoM3/jQdC4qR1SekJgVE29bHH1x3zZAm2OzsKW1ziBViceY/L5Oe2NFMoJSFU1RpYUYnHYQoiV1SG3yPuWa4MVI3nlQb0dnl14ihf0DJaZXCVaMQKBgAhkmNjbBCUzMQOPnqkZrG4HgpU80Q/WOv/OmqpMG89VYNW4uUGAFhfsvy5cUFyelPPxrIGfQNXaBqttC4P0M4XpiEa+9fcWMa7t52dWOOq4vNsGBUX5/WtIQC2XEW7pnKhtXV7vzg5OJvQznkw4G3jy+/uDw9GssKWqrU8Vy6oTAoGABMlqQHUNw9wyehGhP/kja/f/Ml1xxE9z6Tubc6lQptVfx7eCF16Sd5qZXdUtr7BH+B+071oemI5Q3RlmER/8kZ3KuxKKLt9qe6WLitYQHJQ5XR/2im3Eoy6AEJdjVpCac2IyOgLXcK0wyHG1iHHwUS7vKXy3AT6iFx7lTIF69Zs=",

		//编码格式
		'charset' => "UTF-8",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//应用ID
		'app_id' => "2016082600313381",

		//异步通知地址,只有扫码支付预下单可用
		'notify_url' => "http://print.mostclan.com/pay/alipay/notify",

		//最大查询重试次数
		'MaxQueryRetry' => "10",

		//查询间隔
		'QueryDuration' => "3"
);