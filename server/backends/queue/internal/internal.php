<?php

    /**
     * backends queue namespace
     */

    namespace backends\queue
    {
        class internal extends queue
        {
            /**
             * @inheritDoc
             */
            function changed($objectType, $objectId)
            {
                // TODO: Implement changed() method.
            }

            /**
             * @inheritDoc
             */
            function addToQueue($objectType, $objectId, $task, $params = '', $groupId = -1)
            {
                return $this->db->insert("insert into tasks_queue (task_change_id, object_type, object_id, task, params) values (:task_change_id, :object_type, :object_id, :task, :params)", [
                    "task_change_id" => $groupId,
                    "object_type" => $objectType,
                    "object_id" =>$objectId,
                    "task" => $task,
                    "params" => $params,
                ]);
            }

            /**
             * @inheritDoc
             */
            function cron($part)
            {
                if ($part == "minutely") {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
