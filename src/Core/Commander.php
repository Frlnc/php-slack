<?php namespace Frlnc\Slack\Core;

use InvalidArgumentException;
use Frlnc\Slack\Contracts\Http\Interactor;

class Commander {

    /**
     * The default command headers.
     *
     * @var array
     */
    protected static $defaultHeaders = [];

    /**
     * The commands.
     *
     * @var array
     */
    protected static $commands = [
        'api.test' => [
            'endpoint' => '/api.test',
            'token'    => false
        ],
        'auth.test' => [
            'endpoint' => '/auth.test',
            'token'    => true
        ],
        'channels.history' => [
            'token'    => true,
            'endpoint' => '/channels.history'
        ],
        'channels.info' => [
            'token'    => true,
            'endpoint' => '/channels.info'
        ],
        'channels.invite' => [
            'token'    => true,
            'endpoint' => '/channels.invite'
        ],
        'channels.join' => [
            'token'    => true,
            'endpoint' => '/channels.join'
        ],
        'channels.kick' => [
            'token'    => true,
            'endpoint' => '/channels.kick'
        ],
        'channels.leave' => [
            'token'    => true,
            'endpoint' => '/channels.leave'
        ],
        'channels.list' => [
            'token'    => true,
            'endpoint' => '/channels.list'
        ],
        'channels.mark' => [
            'token'    => true,
            'endpoint' => '/channels.mark'
        ],
        'channels.setPurpose' => [
            'token'    => true,
            'endpoint' => '/channels.setPurpose',
            'format'   => [
                'purpose'
            ]
        ],
        'channels.setTopic' => [
            'token'    => true,
            'endpoint' => '/channels.setTopic',
            'format'   => [
                'topic'
            ]
        ],
        'chat.delete' => [
            'token'    => true,
            'endpoint' => '/chat.delete'
        ],
        'chat.postMessage' => [
            'token'    => true,
            'endpoint' => '/chat.postMessage',
            'format'   => [
                'text',
                'username'
            ]
        ],
        'chat.update' => [
            'token'    => true,
            'endpoint' => '/chat.update',
            'format'   => [
                'text'
            ]
        ],
        'emoji.list' => [
            'token'    => true,
            'endpoint' => '/emoji.list'
        ],
        'files.info' => [
            'token'    => true,
            'endpoint' => '/files.info'
        ],
        'files.list' => [
            'token'    => true,
            'endpoint' => '/files.list'
        ],
        'files.upload' => [
            'token'    => true,
            'endpoint' => '/files.upload',
            'post'     => true,
            'headers'  => [
                'Content-Type' => 'multipart/form-data'
            ],
            'format'   => [
                'filename',
                'title',
                'initial_comment'
            ]
        ],
        'groups.create' => [
            'token'    => true,
            'endpoint' => '/groups.create',
            'format'   => [
                'name'
            ]
        ],
        'groups.createChild' => [
            'token'    => true,
            'endpoint' => '/groups.createChild'
        ],
        'groups.history' => [
            'token'    => true,
            'endpoint' => '/groups.history'
        ],
        'groups.invite' => [
            'token'    => true,
            'endpoint' => '/groups.invite'
        ],
        'groups.kick' => [
            'token'    => true,
            'endpoint' => '/groups.kick'
        ],
        'groups.leave' => [
            'token'    => true,
            'endpoint' => '/groups.leave'
        ],
        'groups.list' => [
            'token'    => true,
            'endpoint' => '/groups.list'
        ],
        'groups.mark' => [
            'token'    => true,
            'endpoint' => '/groups.mark'
        ],
        'groups.setPurpose' => [
            'token'    => true,
            'endpoint' => '/groups.setPurpose',
            'format'   => [
                'purpose'
            ]
        ],
        'groups.setTopic' => [
            'token'    => true,
            'endpoint' => '/groups.setTopic',
            'format'   => [
                'topic'
            ]
        ],
        'im.history' => [
            'token'    => true,
            'endpoint' => '/im.history'
        ],
        'im.list' => [
            'token'    => true,
            'endpoint' => '/im.list'
        ],
        'im.mark' => [
            'token'    => true,
            'endpoint' => '/im.mark'
        ],
        'oauth.access' => [
            'token'    => false,
            'endpoint' => '/oauth.access'
        ],
        'search.all' => [
            'token'    => true,
            'endpoint' => '/search.all'
        ],
        'search.files' => [
            'token'    => true,
            'endpoint' => '/search.files'
        ],
        'search.messages' => [
            'token'    => true,
            'endpoint' => '/search.messages'
        ],
        'stars.list' => [
            'token'    => true,
            'endpoint' => '/stars.list'
        ],
        'users.info' => [
            'token'    => true,
            'endpoint' => '/users.info'
        ],
        'users.list' => [
            'token'    => true,
            'endpoint' => '/users.list'
        ],
        'users.setActive' => [
            'token'    => true,
            'endpoint' => '/users.setActive'
        ]
    ];

    /**
     * The base URL.
     *
     * @var string
     */
    protected static $baseUrl = 'https://slack.com/api';

    /**
     * The API token.
     *
     * @var string
     */
    protected $token;

    /**
     * The Http interactor.
     *
     * @var \Frlnc\Slack\Contracts\Http\Interactor
     */
    protected $interactor;

    /**
     * @param string $token
     * @param \Frlnc\Slack\Contracts\Http\Interactor $interactor
     */
    public function __construct($token, Interactor $interactor)
    {
        $this->token = $token;
        $this->interactor = $interactor;
    }

    /**
     * Executes a command.
     *
     * @param  string $command
     * @param  array $parameters
     * @return \Frlnc\Slack\Contracts\Http\Response
     */
    public function execute($command, array $parameters = [])
    {
        if (!isset(self::$commands[$command]))
            throw new InvalidArgumentException("The command '{$command}' is not currently supported");

        $command = self::$commands[$command];

        if ($command['token'])
            $parameters = array_merge($parameters, ['token' => $this->token]);

        if (isset($command['format']))
            foreach ($command['format'] as $format)
                if (isset($parameters[$format]))
                    $parameters[$format] = self::format($parameters[$format]);

        $headers = [];
        if (isset($command['headers']))
            $headers = $command['headers'];

        $url = self::$baseUrl . $command['endpoint'];

        if (isset($command['post']) && $command['post'])
            return $this->interactor->post($url, [], $parameters, $headers);

        return $this->interactor->get($url, $parameters, $headers);
    }

    /**
     * Sets the token.
     *
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Formats a string for Slack.
     *
     * @param  string $string
     * @return string
     */
    public static function format($string)
    {
        $string = str_replace('&', '&amp;', $string);
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('>', '&gt;', $string);

        return $string;
    }

}
