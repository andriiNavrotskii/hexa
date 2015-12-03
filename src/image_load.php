<?php
namespace Hexa;

use Exception;

/**
* Класс загрузки изображения с удаленного хоста
* При инициализации объекта ему передается адрес папки, куда будет сохранятся картинка
* Метод go() принимает первым параметром адрес загружаемой картинки. Второй параметр опциональный - новое имя картинки. Если не указать - сохранит под старым.
* По умолчанию, одноименные картинки быдут перезаписаны. 
* Метод setRewriteOff() отключит перезапись и при загрузке одноименных картинок, к имени будет добавляться номер (n).
* Метод setRewriteOn() снова сключит перезапись.
* @method void setRewriteOn()
* @method void setRewriteOff()
* @method void go(string $input, string $new_name) 
*/
class imageLoad
{
    /*
    * Путь куда надо сохранить
    * @var string
    */
    private $output;

    /*
    * Метка перезаписывать или нет
    * @var boolean
    */
    private $rewrite;

    /*
    * разрешенные типы картинок
    * @var array
    */
    private $image_types = array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG);

    /*
    * Принимает путь куда будет сохранена картинка, после проверки.
    * Запуск метода инициализации  метки перезаписи
    * @param string $output
    */
    function __construct($path)
    {
        if ($this->checkOutput($path)){
            $this->output = $path;
        }

        $this->setRewriteOn();
    }


    /*
    * Устанавливает метку разрешенной перезаписи
    * @param boolean $rewrite
    */
    public function setRewriteOn()
    {
        $this->rewrite = true;
    }

    /*
    * Устанавливает метку запрещенной перезаписи
    * @param boolean $rewrite
    */
    public function setRewriteOff()
    {
        $this->rewrite = false;
    }




    /*
    * Метод копирующий картинку после проверок.
    * Если удачно - возвращает true, если нет - false.
    * Или генерирует ошибку при неверных входных параметрах.
    * @param string $input
    * @param string $new_name 
    * @return boolean
    */
    public function go($input, $new_name = '') 
    {
        // если папки назначения нет, то прекращаем
        if (empty($this->output)){
            return false;
        }

        // инициализация и проверка типа картинки
        $type = $this->initializeType($input);

        
        // если не задано новое имя картинки то формироется имя о основе входного адреса
        // иначе формируется новое имя
        if (empty($new_name)) {
            $name_output = $this->initializeDefaultName($input);   
        } else {
            $name_output = $this->initializeNewName($input, $new_name); 
        }


        // если картинка с таким именем уже существует и метка перезаписи false - вызываю метод формирования нового имени
        if ($this->checkOutputFile($name_output) && !$this->rewrite) {
            $name_output = $this->generateOrderedName($name_output);
        }


        // наконец копирование, если есть все необдходимые параметры  
        try {
            if(!empty($this->output) && !empty($name_output) && !empty($type)) {

                $content = file_get_contents($input);

                if (file_put_contents($this->output . $name_output, $content)){
                    return true;
                } else { 
                    return false;
                }
            }    
            else {
                throw new Exception("Load error");
            }

        }
        catch (Exception $e) {
            echo $e->getMessage(), "\n";
        }    
    }




    /*
    * Возвращает имя картинки отделенное от пути 
    * @param string $input
    * @return string 
    */
    private function initializeDefaultName($input) 
    {
        return basename($input); 
    }


    /*
    * Возвращает новое имя картинки с расширением
    * @param string $input
    * @param string $name
    * @return string 
    */
    private function initializeNewName($input, $name) 
    {
        return $name . strstr(basename($input), ".");
    }




    /*
    * Возвращает и проверяет тип картинки.
    * Если это неразрешенный тип картинки или вообще не картинка - бросает исключение
    * @param string $input
    * @return int $type 
    */
    private function initializeType($input) 
    {
        
        try {
            $type = @exif_imagetype($input);

            if (in_array($type, $this->image_types)) {
                return $type;
            }
            else {
                throw new Exception("Wrong file type");
            }
        }
        catch (Exception $e) {
            echo $e->getMessage(), "\n";
        }            
    }




    /*
    * Проверка пути назначения. Если не существует - бросает исключение.
    * @param string $path
    * @return boolean
    */
    private function checkOutput($path) {

        try {
            
            if (file_exists($path)) {
                return true;
            }
            else {
                throw new Exception("Destination path exist");
                return false;
            }
        }
        catch (Exception $e) {
            echo $e->getMessage(), "\n";
        }
    }


    /*
    * Генерация окончания имени с номером. Для файлов с повторяющимся именем.
    * @param string $name
    * @return string $new_name
    */
    private function generateOrderedName($name)
    {
        $extension = strstr($name, ".");

        $old_name = strstr($name, ".", true);


        $i = 1;
        do {
            $new_name = $old_name . "($i)" . $extension;
            $i++;
        } while ($this->checkOutputFile($new_name));


        return $new_name;
    }



    /*
    * Проверка существования файла с таким именем в в дирректории назначения.
    * @param string $name
    * @return boolean
    */
    private function checkOutputFile($name) {

        $full_name = $this->output . $name;

        return file_exists($full_name);
    }
}
