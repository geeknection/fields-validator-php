<?php
/**
 * Valida os dados de uma requisição
 */
class FieldsValidator {
    private function __construct() {}

    /**
     * Verifica se o campo é requerido
     * @return boolean
     */
    private static function isRequired(array $filters = array(
        'types' => array(),
        'field' => null,
        'data' => array()
    )) {
        $required = in_array('required', $filters['types']);
        if (!$required) return false;
        
        if (!isset($filters['data'][$filters['field']])) return true;

        $type = gettype($filters['data'][$filters['field']]);
        $content = $filters['data'][$filters['field']];

        if ($type === 'boolean') return false;
        if ($type === 'integer') return false;

        if (($type === 'array') && count($content) === 0) return true;
        if (($type === 'string') && strlen($content) === 0) return true;
        if ($type === NULL) return true;
        if ($type === 'unknown type') return true;

        return false;
    }
    /**
     * Verifica se o tipo do campo é válido
     * @return boolean
     */
    private static function checkType(array $filters = array(
        'types' => array(),
        'field' => null,
        'data' => array()
    )) {
        $valid = true;
        foreach ($filters['types'] as $key => $type) {
            if ($type !== 'required') {
                if (isset($filters['data'][$filters['field']])) {
                    $filterType = gettype($filters['data'][$filters['field']]);
                    if ($filterType !== $type) $valid = false;
                }
            }
        }
        return $valid;
    }

    /**
     * Valida os campos
     * @return array
     */
    public static function check(array $items, array $data) {
        $errors = array();
        $result = array();

        foreach ($items as $fields) {
            foreach ($fields as $key => $field) {
                $types = explode("|", $key);
    
                $required = self::isRequired(array(
                    'types' => $types,
                    'field' => $field,
                    'data' => $data
                ));
                if ($required) array_push($errors, $field);
    
                $typeValid = self::checkType(array(
                    'types' => $types,
                    'field' => $field,
                    'data' => $data
                ));
    
                if (!$typeValid && !array_search($field, $errors)) array_push($errors, $field);
    
                $result[$field] = isset($data[$field]) ? $data[$field] : NULL;
            }
        }

        if (count($errors) > 0) throw new ExceptionWithData("Parâmetros inválidos", $errors);

        return $result;
    }
}
?>