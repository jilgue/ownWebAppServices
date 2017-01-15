<?php

/**
 * Clase objecto
 */
abstract class Object {

    // Se guarda toda la jerarquia de la clase
    var $hierarchy = array();

    public $objFields = array();

    var $params = array();

    // Sirve para saber si el objecto esta correcto
    var $ok = true;

    protected function __construct($params = array()) {

        $this->_loadHierarchy();

        $this->_autoloadParams($params);
    }

    /**
     * Carga en $this->hierarchy la jerarquía de la clase
     * return null
     */
    private function _loadHierarchy() {

        $class = get_class($this);
        $this->hierarchy = Object::stGetHierarchy($class);
    }


    /**
     * Carga en this los parametros que llegan al constructo
     * return null
     */
    private function _autoloadParams($params) {

        if (is_array($params)) {
            foreach ($params as $param => $value) {

                $this->$param = $value;
            }
        }

        $this->params = $params;

        foreach ($this->objFields as $objField => $value) {
            if (!isset($this->$objField)
                // Si es un DT no se autocarga
                && !isset($value["DT"])) {
                $this->$objField = $value;
            }
        }
    }

    /**
     * Carga en this los parametros que llegan al constructo
     * return null
     */
    final static private function _stFinalConstruct($object, $params = array()) {

        // El objecto esta mal y nos viene ya sin parámetros, lo devolvemos vacio
        if ($object->ok === false
            && count($params) != 0) {
            $class = get_class($object);
            return $class::stVirtualConstructor();
        }

        return $object;
    }

    static function stVirtualConstructor($params = array()) {

        $class = get_called_class();
        $args = func_get_args();
        $numArgs = count($args);

        if ($numArgs == 1) {
            if (is_array($args[0])) {

                if ($args[0] == $params) {
                    return Object::_stFinalConstruct(new $class($params), $params);
                }

                // Ok, nos han llamado de la forma "normal": un único parámetro de tipo array
                return Object::_stFinalConstruct(new $class(array_merge($args[0], $params)), $params);
            }
        }

        if ($numArgs == 0) {
            return Object::_stFinalConstruct(new $class($params), $params);
        }

        // Si no es así pasamos todos los parametros, cada clase sabra que hacer con ellos, por el bien que le trae xD
        if (is_array($params)) {
            return Object::_stFinalConstruct(new $class(array_merge($args, $params)), $params);
        }

        return Object::_stFinalConstruct(new $class($args), $params);
    }

    static function stGetObjFields() {

        $class = get_called_class();

        $objFields = array();
        foreach (Object::stGetHierarchy($class) as $class) {

            $reflex = new ReflectionClass($class);

            $properties = $reflex->getDefaultProperties();

            $objFields = array_merge($objFields, $properties["objFields"]);
        }
        return $objFields;
    }

    static function stGetHierarchy($class) {

        $ret = array($class);

        while (($class = get_parent_class($class)) !== false) {
            $ret[] = $class;
        }

        return $ret;
    }

    /**
     * Lo usamos para definir getter y setters automáticamente
     */
    public function __call($method, $arguments) {

        if (preg_match("#^(.+)_cached$#", $method, $matches)) {
            // Funcion que va ir cacheada
            return $this->_getCachedFuncion($matches[1], $arguments);
        }

        return $this->_callSetGet($method, $arguments);
    }

    private function _callSetGet($method, $arguments) {

        if (!preg_match("#^(get|set|_get|_set)(.+)$#", $method, $matches)) {
            // Método desconocido
            return null;
        }

        $op = $matches[1];
        $capturedField = lcfirst($matches[2]);

        if (!isset($this->$capturedField)) {
            return null;
        }

        switch ($op) {
            case "get":
            case "_get":
                if (count($arguments) != 0) {
                    return null;
                }

                return $this->_getter($capturedField);
		break;

            case "set":
            case "_set":
                if (count($arguments) != 1) {
                    return null;
                }

                return $this->_setter($capturedField, $arguments[0]);
		break;

            default:
                return null;
                break;
        }
    }

    private function _getter($field) {

        if (isset($this->$field)) {
            return $this->$field;
        }

        return false;
    }

    private function _setter($field, $value) {

        if (isset($this->$field)) {
            $this->$field = $value;
            return (bool) $this->$field === $value;
        }
        return false;
    }

    function multiSetter($params) {

        foreach ($params as $field => $value) {
            if (!$this->_setter($field, $value)) {
                return false;
            }
        }

        return true;
    }

    private function _getCachedFuncion($function, $args) {

        static $stCache = array();

        // TODO pasar en args el tiempo de cache y quitar de los args de la funcion
        $cacheId = md5(serialize($function) . serialize($args));

        // TODO cachear de verdad
        if (isset($stCache[$cacheId])) {
            return $stCache[$cacheId];
        }

        $ret = call_user_func_array(array($this, $function), $args);

        $stCache[$cacheId] = $ret;

        return $ret;
    }
}
