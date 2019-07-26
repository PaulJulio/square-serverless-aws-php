# Creating PHP binary 

1. Create a new pem file, download it to ~/.ssh/aws-key.pem and ensure its permissions are set to 400 (AWS Console -> EC2 -> Network & Security -> Key Pairs)
2. Create a EC2 large instance using the AMI listed in [the AWS docs][2])
3. Copy compile_php.sh to instance, compile php, and retrive the compiled binary with the following instructions.

    From this project's root directory:
    
        $ export AWS_IP=ec2-user@{ipaddress}
        $ export SSH_KEY_FILE=~/.ssh/aws-key.pem
        $ scp -i $SSH_KEY_FILE doc/compile_php.sh $AWS_IP:compile_php.sh
        $ ssh -i $SSH_KEY_FILE -t $AWS_IP "chmod a+x compile_php.sh && ./compile_php.sh 7.3.7"
        $ scp -i $SSH_KEY_FILE $AWS_IP:php-7-bin/bin/php layer/php/php

    (Replace `{ipaddress}` with the IP address of your EC2 instance)

3. Shutdown the EC2 instance


(Full details in [AWS Lambda Custom Runtime for PHP: A Practical Example][1])

[1]: https://aws.amazon.com/blogs/apn/aws-lambda-custom-runtime-for-php-a-practical-example/
[2]: https://docs.aws.amazon.com/lambda/latest/dg/current-supported-versions.html
