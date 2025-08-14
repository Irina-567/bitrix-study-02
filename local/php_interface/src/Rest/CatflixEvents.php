<?php

namespace Rest;

use Bitrix\Main\Localization\Loc;
use Bitrix\Rest\RestException;
use Bitrix\Main\Event;
use Models\CatflixStreamsTable;

class CatflixEvents
{
    public static function OnRestServiceBuildDescriptionHandler()
    {
        Loc::loadMessages(__FILE__);
        Loc::getMessage('REST_SCOPE_CATFLIX.STREAMS');

        return [
            'catflix.streams' => [
                'catflix.streams.add'    => [__CLASS__, 'add'],
                'catflix.streams.list'   => [__CLASS__, 'list'],
                'catflix.streams.update' => [__CLASS__, 'update'],
                'catflix.streams.delete' => [__CLASS__, 'delete'],
            ],
        ];
    }

    /**
     * Adds a new stream
     *
     * @param array $params
     * @param mixed $nav
     * @param \CRestServer $server
     * @return array
     * @throws \Bitrix\Rest\RestException
     */
    public static function add($params, $nav, \CRestServer $server)
    {
        self::log('add', $params);

        $result = \Models\CatflixStreamsTable::add($params);

        // Log result
        self::log('add_result', [
            'success' => $result->isSuccess(),
            'id' => $result->getId(),
            'errors' => $result->getErrorMessages()
        ]);


        if ($result->isSuccess()) {
            return ['result' => 'OK', 'id' => $result->getId()];
        }

        throw new \Bitrix\Rest\RestException(
            json_encode($result->getErrorMessages(), JSON_UNESCAPED_UNICODE),
            \Bitrix\Rest\RestException::ERROR_ARGUMENT,
            \CRestServer::STATUS_OK
        );
    }

    /**
     * List all streams
     *
     * @param array $params
     * @param mixed $nav
     * @param \CRestServer $server
     * @return array
     * @throws \Bitrix\Rest\RestException
     */
    public static function list($params, $nav, \CRestServer $server)
    {
        self::log('list', $params);

        $result = \Models\CatflixStreamsTable::getList([
            'select' => ['*'],
            'order' => ['ID' => 'DESC'],
            'limit' => 50,
        ]);

        $items = [];
        while ($row = $result->fetch()) {
            $items[] = $row;
        }

        return $items;
    }

    /**
     * Update stream
     *
     * @param array $params
     * @param mixed $nav
     * @param \CRestServer $server
     * @return array
     * @throws \Bitrix\Rest\RestException
     */
    public static function update($params, $nav, \CRestServer $server)
    {
        self::log('update', $params);

        $id = (int)$params['ID'];
        unset($params['ID']);

        if ($id <= 0) {
            throw new \Bitrix\Rest\RestException('Missing or invalid ID');
        }

        $result = \Models\CatflixStreamsTable::update($id, $params);

        if ($result->isSuccess()) {
            return ['result' => 'OK'];
        }

        throw new \Bitrix\Rest\RestException(
            json_encode($result->getErrorMessages(), JSON_UNESCAPED_UNICODE),
            \Bitrix\Rest\RestException::ERROR_ARGUMENT,
            \CRestServer::STATUS_OK
        );
    }

    /**
     * Delete stream
     *
     * @param array $params
     * @param mixed $nav
     * @param \CRestServer $server
     * @return array
     * @throws \Bitrix\Rest\RestException
     */
    public static function delete($params, $nav, \CRestServer $server)
    {
        self::log('delete', $params);

        $id = (int)$params['ID'];

        if ($id <= 0) {
            throw new \Bitrix\Rest\RestException('Missing or invalid ID');
        }

        $result = \Models\CatflixStreamsTable::delete($id);

        if ($result->isSuccess()) {
            return ['result' => 'OK'];
        }

        throw new \Bitrix\Rest\RestException(
            json_encode($result->getErrorMessages(), JSON_UNESCAPED_UNICODE),
            \Bitrix\Rest\RestException::ERROR_ARGUMENT,
            \CRestServer::STATUS_OK
        );
    }

    /**
     * Log REST request/response
     */
    protected static function log($label, $data)
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/catflix_rest.log';
        file_put_contents($path, date('Y-m-d H:i:s') . " [$label]: " . print_r($data, true) . "\n", FILE_APPEND);
    }
}
