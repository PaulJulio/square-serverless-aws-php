# AWS Lambda PHP Hello World

The basics of using [Serverless Framework][1] for AWS Lambda PHP applications.

## Notes

1. Install Serverless Framework by following the [Quick Start][2]
2. Set up your [AWS credentials][3]
2. Create php binary by following steps in [`doc/create_php_binary.md`][4]
2. Create your Square Application, store your keys in AWS Secrets Manager (client-id, client-secret json format) TODO: spell this process out
2. Write your serverless application (!) - the default is in `handler.php`
2. Run `sls deploy` to deploy to Lambda
2. Run `sls invoke -f hello -l` to invoke your function
2. In this example, static web assets are stored with the project and served by lambdas. See the ['static/README.md'][5] for more info.

## PHP handler function signature

The signature for the PHP function is:

    function main($eventData) : array

Hello world looks like:

    <?php
    function hello($eventData) : array
    {
        return ["msg" => "Hello from PHP " . PHP_VERSION];
    }


[1]: https://serverless.com
[2]: https://serverless.com/framework/docs/providers/aws/guide/quick-start/
[3]: https://serverless.com/framework/docs/providers/aws/guide/credentials/
[4]: doc/create_php_binary.md
[5]: static/README.md
