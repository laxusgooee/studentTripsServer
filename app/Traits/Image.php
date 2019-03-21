<?php

namespace App\Traits;

trait Image
{
    //protected static $imagePath;
    //protected static $imageColumn;

    private $folder = '';//add 'public' for dev

    //defaults
    private static $defaults = [
        'imageColumn' => 'image',
        'strict' => false
    ];

    protected function imageColumn()
    {
        $imageColumn = static::$imageColumn;

        if(empty($imageColumn))
            $imageColumn = self::$defaults['imageColumn'];

        return $imageColumn;
    }
    
	public function uploadImage($image, $oimg = null)
    {
        $imagePath = static::$imagePath;
        $imageColumn = $this->imageColumn();

        $sub = env('APP_SUB');//cause of project folder holder
        
        $abs_path = str_replace($sub, '', base_path($this->folder.$imagePath));

        if(!empty($oimg))
            $imageName = $oimg;
        else
            $imageName = !empty($this->$imageColumn)? $this->$imageColumn : time().'.'.$image->getClientOriginalExtension();

        $image->move( $abs_path, $imageName);
        $this->$imageColumn = $imageName;
        $this->updated_at = date('Y-m-d H:i:s');

        return $this;
    }


	public function imagePath($abs = false, $strict = false, $name = '')
    {
        $imageColumn = $this->imageColumn();
        $name = empty($name)? $this->$imageColumn: $name;

        if(is_url($name))
            return $name;
        
        $img = static::$imagePath.'/'.$name;

        $sub = env('APP_SUB');

        $ass_path = asset($img);
        $abs_path = str_replace($sub, '', base_path($this->folder.$img));

        if (file_exists( $abs_path ) && !empty($name)) {
            return ($abs)? $abs_path : $ass_path;
        } else {
            return ($strict)? null : asset(static::$imagePath.'/default.jpg');
        }
    }
}
