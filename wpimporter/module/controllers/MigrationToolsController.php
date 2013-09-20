<?php

class MigrationToolsController
{


    public static function saveBD()
    {
        $mysqlExportPath = '../data/sql/baseDB.sql';

        $command = MYSQLDUMP." --opt --host=".HOST." --user=".LOGIN." --password=".PASSWORD." ".BD_NAME_WP." > ".$mysqlExportPath;
        exec($command, $output=array(), $worked);

        switch ($worked) {
        case 0:
            echo 'Database <b>'.BD_NAME_WP.'</b> successfully exported to <b>'.$mysqlExportPath.'</b>';
            break;
        case 1:
            echo 'There was a warning during the export of <b>'.BD_NAME_WP.'</b> to <b>'.$mysqlExportPath.'</b>';
            break;
        case 2:
            echo 'There was an error during export. Please check your values:<br/><br/><table><tr><td>MySQL Database Name:</td><td><b>'.BD_NAME_WP.'</b></td></tr><tr><td>MySQL User Name:</td><td><b>'.USER.'</b></td></tr><tr><td>MySQL Password:</td><td><b>NOTSHOWN</b></td></tr><tr><td>MySQL Host Name:</td><td><b>'.HOST.'</b></td></tr></table>';
            break;
        }


    }

    public static function restoreDB()
    {
        $mysqlImportFilename = '../data/sql/baseDB.sql';

        $command = MYSQL.' --host='.HOST.' --user='.LOGIN.' --password='.PASSWORD.' '.BD_NAME_WP.' < '.$mysqlImportFilename;
        exec($command, $output = array(), $worked);
        switch ($worked) {
        case 0:
            echo 'Import file <b>'.$mysqlImportFilename.'</b> successfully imported to database <b>'.BD_NAME_WP.'</b>';
            break;
        case 1:
            echo 'There was an error during import. Please make sure the import file is saved in the same folder as this script and check your values:<br/><br/><table><tr><td>MySQL Database Name:</td><td><b>'.$mysqlDatabaseName.'</b></td></tr><tr><td>MySQL User Name:</td><td><b>'.$mysqlUserName.'</b></td></tr><tr><td>MySQL Password:</td><td><b>NOTSHOWN</b></td></tr><tr><td>MySQL Host Name:</td><td><b>'.$mysqlHostName.'</b></td></tr><tr><td>MySQL Import Filename:</td><td><b>'.$mysqlImportFilename.'</b></td></tr></table>';
            break;
        }

    }

}