<?php
namespace model;

class ReportManager extends \framework\Manager 
{
    protected $entities = 'reports';

    public function getList($debut, $fin, $filters = []){
        $sql = 'SELECT * FROM ' . $this->table;

        if (!empty($filters)) {
            $sql .= ' WHERE ';
            foreach ($filters as $key => $filter) {
                $sql .= $key . $filter . ' AND ';
            }
            $sql = substr($sql, 0, -5);
        }

        $sql .= ' ORDER BY reportDate DESC';

        if ($debut !== null && $limit !== null) {
            $sql .= ' LIMIT ' .(int) $limit.' OFFSET '.(int) $debut; 
        }

        $req = $this->dao->query($sql);
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'entity\Report');
        $reports = $req->fetchAll();

        foreach ($reports as $report)
        {
            $report->setReportDate(new \DateTime($report->reportDate()));
        }
        
        $req->closeCursor();
        
        return $reports;
    }

    public function countComments() {
        $sql = 'SELECT COUNT(*) FROM ' . $this->table . ' GROUP BY commentId';
        
        return (int)$this->dao->query($sql)->fetchColumn();
    }

    public function add($authorId, $commentId, $content, $commentContent)
    {
        $sql = 'INSERT INTO ' . $this->table . ' SET authorId = :authorId, commentId = :commentId, content = :content, commentContent = :commentContent, reportDate = NOW()';

        $q = $this->dao->prepare($sql);
        
        $q->bindValue(':authorId', $authorId);
        $q->bindValue(':commentId', $commentId);
        $q->bindValue(':content', $content);
        $q->bindValue(':commentContent', $commentContent);
        
        $q->execute();
    }

    public function getId($commentId, $authorId)
    {
        $sql = 'SELECT id FROM ' . $this->table . ' WHERE commentId=' . $commentId . ' AND authorId=' . $authorId;

        return $this->dao->query($sql)->fetchColumn();
    }

    public function getSingle($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';

        $req = $this->dao->prepare($sql);
        $req->bindValue(':id', (int) $id, \PDO::PARAM_INT);
        $req->execute();
        
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, 'entity\Report');
        
        if ($report = $req->fetch())
        {
            $report->setReportDate(new \DateTime($report->reportDate()));
            return $report;
        }
        
        return null;  
    }

    public function clear($commentId)
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE commentId = '.(int) $commentId;
        $this->dao->exec($sql);
    }
}