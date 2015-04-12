<?php

class deploy
{
    const BRANCH = 'master';
    const DEPLOYMENTPATH = '/var/www';

    /**
     * @var
     */
    private  $ipsBitbucket;

    /**
     * @var
     */
    private $branch;

    /**
     * @var
     */
    private $path;

    public function __construct()
    {
        $this->ipsBitbucket = ['131.103.20.165','131.103.20.166'];
        $this->branch = self::BRANCH;
        $this->path = self::DEPLOYMENTPATH;
    }

    /**
     * @param mixed $ipsBitbucket
     */
    public function setIpsBitbucket($ipsBitbucket)
    {
        $this->ipsBitbucket = $ipsBitbucket;
    }

    /**
     * @return mixed
     */
    public function getIpsBitbucket()
    {
        return $this->ipsBitbucket;
    }

    /**
     * @param mixed $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return mixed
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    public function checkSecurity()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])
            || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            || !(in_array(@$_SERVER['REMOTE_ADDR'], $this->ipsBitbucket) || php_sapi_name() === 'cli-server')
        ) {
            throw new Exception('You are not allowed to access this file.');
        }
    }

    public function execute()
    {
        if (isset($_POST['payload'])) {
            $bitbucketRequest = json_decode($_POST['payload'],true);
            if ($bitbucketRequest["commits"]) {
                $thereAreCommit = false;
                foreach ($bitbucketRequest["commits"] as $commit) {
                    if ($commit["branch"] == $this->branch) {
                        $thereAreCommit = true;
                    }
                }
                if ($thereAreCommit) {
                    $command = 'cd '.$this->path.'  && git pull';
                    $tmp = shell_exec($command);
                    $output = htmlentities(trim($tmp)) . "\n";
                    echo $output;
                }
            }
        }
    }

}

$deploy = new deploy();
$deploy->checkSecurity();
$deploy->execute();
