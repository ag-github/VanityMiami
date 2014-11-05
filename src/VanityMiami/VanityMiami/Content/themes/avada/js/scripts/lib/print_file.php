<?php
require_once 'lib/fast_init.php';

class Lib_PrintFile {
    /**
     * @var Lib_Db
     */
    private $db;
    private $fileId;
    private $isAttachment;

    public function __construct($fileId, $isAttachment = true) {
        $this->fileId = $fileId;
        $this->isAttachment = $isAttachment;
        $settings = new Lib_SettingFile();
        $settings->load();
        $this->db = $settings->getDb();
    }

    public function printContent() {
        try {
            $this->printFileContent($this->db->getOneRow('SELECT * FROM qu_g_files WHERE fileid = \''.$this->db->escape($this->fileId).'\''));
        } catch (Exception $e) {
            echo 'File was removed';
        }
    }

    private function setHeader($name, $value) {
        header($name . ': ' . $value, true, null);
    }

    private function printHeaders(array $fileArray) {
        $this->setHeader("Content-Type", $fileArray['filetype']);
        if ($this->isAttachment) {
            $this->setHeader('Cache-Control', 'private, must-revalidate');
            $this->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');
            $this->setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
            $this->setHeader("Content-Transfer-Encoding", 'binary');

            $this->setHeader('Content-Description', 'File Transfer');
            $this->setHeader('Content-Type', 'application/force-download');
            $this->setHeader('Content-Disposition', 'attachment; filename="'
            . htmlspecialchars($fileArray['filename']) . '"');
        } else {
            $cacheExpire = 300;
            $this->setHeader('Cache-Control', 'max-age='.$cacheExpire);
            $this->setHeader('Expires', date(DATE_RFC822, time()+$cacheExpire));
            $this->setHeader('Content-Disposition', 'filename="' . htmlspecialchars($fileArray['filename']) . '"');
        }
        $this->setHeader('Content-Length', $fileArray['filesize']);
    }

    private function printFileContent(array $fileArray) {
        if (!strlen($fileArray['path'])) {
            $this->printDB($fileArray);
            return;
        }
        if (substr($fileArray['path'], 0, 4) == 'http') {
            $this->setHeader('Location', $fileArray['path']);
            return;
        }
        $this->printFileSystem($fileArray);
    }

    private function printDB(array $fileArray) {
        $result = $this->db->getRows('SELECT content FROM qu_g_filecontents WHERE fileid = \''.$this->db->escape($fileArray['fileid']).'\' ORDER BY contentid ASC');
        if ($result->rowCount() > 0) {
            $this->printHeaders($fileArray);
            while ($fileContent = $result->fetchArray()) {
                echo $fileContent['content'];
            }
        } else {
            echo 'File content does not exist.';
        }
    }

    private function printFileSystem(array $fileArray) {
        $fileName = rtrim($fileArray['path'], '/\\');
        if (($fileHandler = @fopen($fileName, 'r')) !== false) {
            $this->printHeaders($fileArray);
            fpassthru($fileHandler);
            flush();
        } else {
            echo 'You do not have permissions for read file.';
        }
    }
}
?>
