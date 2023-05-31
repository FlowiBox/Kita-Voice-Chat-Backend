<?php

namespace App\Services;

use Illuminate\Contracts\Config\Repository;
use App\Repositories\Conversation\ConversationRepository;

class Chat
{
    protected $config;

    protected $conversation;

    protected $userId;

    protected $group;

    /**
     * Chat constructor.
     *
     * @param Repository                  $config
     * @param ConversationRepository      $conversation
     */
    public function __construct(
        Repository $config,
        ConversationRepository $conversation
    ) {
        $this->config = $config;
        $this->conversation = $conversation;
        $this->userId = check() ? check()->user()->id : null;
        $this->group = $group;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAllConversations()
    {
        return $this->conversation->getAllConversations($this->userId);
    }

    /**
     * @param $conversationId
     *
     * @return object
     */
    public function getConversationMessageById($conversationId)
    {
        if ($this->conversation->checkUserExist($this->userId, $conversationId)) {
            $channel = $this->getChannelName($conversationId, 'chat_room');

            return $this->conversation->getConversationMessageById($conversationId, $this->userId, $channel);
        }

        abort(404);
    }

    /**
     * @param $conversationId
     * @param $text
     */
    public function sendConversationMessage($conversationId, $text)
    {
        $this->conversation->sendConversationMessage($conversationId, [
            'text'    => $text,
            'user_id' => $this->userId,
            'channel' => $this->getChannelName($conversationId, 'chat_room'),
        ]);
    }

    /**
     * @param $userId
     */
    public function startConversationWith($userId)
    {
        $this->conversation->startConversationWith($this->userId, $userId);
    }

    /**
     * @param $conversationId
     */
    public function acceptMessageRequest($conversationId)
    {
        $this->conversation->acceptMessageRequest($this->userId, $conversationId);
    }

    /**
     * @param $conversationId
     * @param $type
     *
     * @return string
     */
    private function getChannelName($conversationId, $type)
    {
        return $this->config->get('chat.channel.'.$type).'-'.$conversationId;
    }

    /**
     * @param $conversationId
     * @param $file
     */
    public function sendFilesInConversation($conversationId, $file)
    {
        $this->sendFiles($conversationId, $file, 'conversation');
    }

    private function sendFiles($id, $file, $type)
    {
        switch ($type) {
            case 'conversation':
                $this->conversation->sendFilesInConversation($id, [
                    'file'    => $file,
                    'text'    => 'File Sent',
                    'user_id' => $this->userId,
                    'channel' => $this->getChannelName($id, 'chat_room'),
                ]);
                break;
        }
    }
}