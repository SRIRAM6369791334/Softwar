<?php

namespace App\Core;

/**
 * Undo Manager [#97]
 * Allows reverting critical actions by replaying previous state.
 */
class UndoManager
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Revert an action from admin_actions
     */
    public function undo(int $actionId): bool
    {
        $action = $this->db->query(
            "SELECT * FROM admin_actions WHERE id = ?",
            [$actionId]
        )->fetch();

        if (!$action || empty($action['previous_state'])) {
            return false;
        }

        $table = $action['target_type'];
        $targetId = $action['target_id'];
        $previousState = json_decode($action['previous_state'], true);

        if (!$previousState) return false;

        // Perform the revert
        $setClauses = [];
        $params = [];
        foreach ($previousState as $col => $val) {
            $setClauses[] = "`$col` = ?";
            $params[] = $val;
        }
        $params[] = $targetId;

        $sql = "UPDATE `$table` SET " . implode(', ', $setClauses) . " WHERE id = ?";
        $this->db->query($sql, $params);

        // Mark action as reverted
        $this->db->query("UPDATE admin_actions SET action = 'REVERTED' WHERE id = ?", [$actionId]);
        
        return true;
    }
}
