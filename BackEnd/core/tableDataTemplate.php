<?php
declare(strict_types=1);
require_once __DIR__ . '/./safeHTML.php';

class TableCreator {
    public function returnVerticalTables(array $keys, array $values, ?string $rowname = null) : string {
        $assoc = array_combine($keys, $values);
        $html = '';
        foreach($assoc as $key=> $value) {
            $keyVal = (is_numeric($key) || $key instanceof SafeHTML) ? $key : htmlspecialchars($key) ;
            $val = (is_numeric($value) || $value instanceof SafeHTML) ? $value : htmlspecialchars($value);
            //start creating table
            $html .= is_null($rowname) ? '<tr>' : '<tr class="'.$rowname.'">';
            $html .= '<td>'.$keyVal.'</td>';
            $html .= '<td>'.$val.'</td>';
            $html .= '</tr>';
        } 
        return $html;
    }
    //Used for concatenating directly
    public function returnHorizontalTitles(array $titles, ?string $rowName = null) : string {
        $html = '';
        $html .= '<thead>';
        $html .= is_null($rowName) ? '<tr>' : '<tr class="'.$rowName.'">';
        foreach($titles as $rows) {
            // Convert to string if it's not already
            $rowValue = is_numeric($rows) ? (string)$rows : (is_string($rows) ? $rows : '');
            $html .= '<th> ' .htmlspecialchars($rowValue) . '</th>';
        }
        $html .= '</tr>';
        $html .= '</thead>';

        return $html;
    }
    public function returnHorizontalRows(array $values, ?string $className =null) {
        $html = '';
        $html .= is_null($className) ? '<tr>' : '<tr class="'.$className.'">';
        foreach($values as $rows) {
            if(is_numeric($rows) || $rows instanceof SafeHTML) {
                $cleanedRow = $rows;
            }
            else {
                $cleanedRow = htmlspecialchars($rows);
            }
            $html .= '<td>' .$cleanedRow. '</td>';
        }
        $html .= '</tr>';
        return $html;
    }
}