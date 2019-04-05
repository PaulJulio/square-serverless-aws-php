# Square Serverless with AWS Lambda and PHP

The basics of using [Serverless Framework][1] for AWS Lambda PHP applications for Square.

## Notes

1. Install Serverless Framework by following the [Quick Start][2]
2. Set up your [AWS credentials][3]
2. Create php binary by following steps in [`doc/create_php_binary.md`][4] *for hack week, this is included*
2. Create your Square Application, store your keys in AWS Secrets Manager  by following the steps in [`doc/secrets.md`][7]
2. Update your dependencies with [composer][6]
2. Write your serverless application (!) - the default is in `handler.php`
2. Run `sls deploy` to deploy to Lambda
2. Run `sls invoke -f hello -l` to invoke your function
2. In this example, static web assets are stored with the project and served by lambdas. See the [`static/README.md`][5] for more info.

## ToDos

1. Local testing with SAM needed to reduce test-cycle time
2. Static assets could all be served with one route if better mime-type logic were added to that route
2. Local testing with [AWS SAM][8] would really help with the dev cycle

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
[6]: https://getcomposer.org
[7]: doc/secrets.md
[8]: https://aws.amazon.com/blogs/aws/new-aws-sam-local-beta-build-and-test-serverless-applications-locally/
