<?php namespace Classes;

date_default_timezone_set('America/Los_Angeles');
/**
 *    
 */
//ffmpeg -i in.mp4 -ss start -t duration -c:v copy -c:a copy out.mp4
//ffmpeg -i input.mov -ss 00:00:00 -t 00:00:02 -c:v copy -c:a copy output.mov

class Video {
    private $original_video;
    private $metadata;
    private $title;
    // private $web_safe_title;
    private $project;
    // private $review_stage;
    // private $version;
    private $description;
    private $duration;
    private $video_srcs;
    private $outputfile;
    private $pidfile;
    private $id;
    private $video;
    private $video_type;

    #public static $upload_dir = $_SERVER['DOCUMENT_ROOT']."/../uploads/videos/";
    // private $text_files_dir;
    private $video_dir;
    private $shell_scripts_dir; 

    function __construct($video_id, $video_type) {
        $this->video = \Video::find($video_id);

        //create empty row in Video table to generate an id
        // $new_video = \Video::create(array());
        // $this->id = $new_video->id;

        // $title = $video->metadata->getClientOriginalName();
        // //extract the title from the video file
        // $title = explode('.', $title);
        // //check for spaces and hyphens and replace them with underscores
        // $title = preg_replace('![^\w]!', "_", strtolower($title[0]));
        //
        // if ($this->id > 1 || $this->id < 9)
        //     $this->title = date('Ymd').'0'. $this->id.'_'.$title;
        // else 
        //     $this->title = date('Ymd'). $this->id.'_'.$title;

        //using PHP namespace 
        // $this->original_video = new \stdClass();
        // $this->original_video->file = $video->metadata;
        $this->video_type       = $video_type;
        $this->description      = $this->video->description;
        $this->video_dir        = $this->video->video_dir;
        $this->shell_scripts_dir= dirname(dirname(__FILE__)). "/shell_scripts/";

        // if ( !is_dir( $this->video_dir ) )
        //     mkdir( $this->video_dir, 0777, true );

//         if ( !is_dir( $this->text_files_dir ) )
//             mkdir( $this->text_files_dir, 0777, true );

        // $this->project = $data->project;
        // $this->review_stage = $data->review_stage;
        // $this->version = $data->version;
        // $this->duplicate = $data->duplicate;
    }

    # returns the metadata pertaining to the first video stream found in the meta text
    public static function findVideoStream($meta) {
        # Match text where a line begins with 'Stream' followed by the stream number and the word 'Video' until the next 'Stream' found
        #'/^\s*Stream #\d+:\d+[^:]+: Video(?:.(?!Stream|At least one output file must be specified))+./ms'
        // if (preg_match('/^\s*Stream #\d+:\d+[^:]+: Video:\s*(.+)/m', $meta, $video_stream)) {
        if (preg_match('/^\s*Stream #\d+:\d?:?.+: Video:\s*(.+)/m', $meta, $video_stream)) {


            $video_metadata = new \stdClass();

                
            // $stream_data = preg_split('/\s*,\s*/', $matches[1]);
            // mathces 3 or more digis follow by x follow by 3 or more digits
            if (preg_match('/\d{3,}x\d{3,}/m', $video_stream[1], $dimensions)) {
                $dimensions = explode('x', $dimensions[0]);
                $video_metadata->width = $dimensions[0];
                $video_metadata->height = $dimensions[1];
            }

            if (preg_match('/[^ ]*/m', $video_stream[1], $codec)) {
                $video_metadata->codec = $codec[0];
            }

            if (preg_match('/\d{1,} kb\/s/m', $video_stream[1], $bitrate)) {
                $bitrate = explode(' ', $bitrate[0]);
                $video_metadata->bitrate = $bitrate[0];
            }    

            if (preg_match('/(\d{1,}\.\d{1,} fps)/m', $video_stream[1], $framerate)) {
                $framerate = explode(' ', $framerate[0]);
                $video_metadata->framerate = $framerate[0];
            }else{
                if (preg_match('/\d{1,}. tbr/m', $video_stream[1], $framerate)) {
                    $framerate = explode(' ', $framerate[0]);
                    $framerate = preg_replace('/k/m', "000", $framerate[0]);
                    // echo $framerate;
                    $video_metadata->framerate = $framerate;
                } 
            }              
            return $video_metadata;
        }
        return null;
    }

    // public static function findAudioStream($meta) {
    //     if (preg_match('/^\s*Stream #\d+:\d+[^:]+: Audio:\s*(.+)/m', $meta, $matches)) {
    //         $stream_data = preg_split('/\s*,\s*/', $matches[1]);
    //         
    //         $stream = new \stdClass();
    //         
    //         $stream->codec = $stream_data[0];
    //         
    //         $sample_rate = substr($stream_data[1], 0, strpos($stream_data[1], " "));
    //         $stream->sample_rate = (float) $sample_rate;
    //         
    //         $stream->channels = $stream_data[2];
    //         
    //         $sample_size = substr($stream_data[3], strpos($stream_data[3], "s") + 1);
    //         $stream->sample_size = (float) $sample_size;
    //         
    //         $bitrate = substr($stream_data[4], 0, strpos($stream_data[4], " "));
    //         $stream->bitrate = (float) $bitrate;
    //         
    //         return $stream;
    //     }
    //     return null;
    // }

    private function loadMetaData($path) {
        // $webroot = $_SERVER['DOCUMENT_ROOT'];
        // $scripts = $webroot."/../resources/scripts/";
        #exec(". ".$scripts."get_video_metadata.sh '".$path."'", $meta);
        
        // $cmd = ". ".$this->shell_scripts_dir."get_video_metadata.sh '".$path."'";
        $cmd = "/usr/local/bin/ffmpeg -i ".$path." 2>&1";
        #$outputfile = $this->upload_dir.$this->web_safe_title."_progress.txt";
        
        $meta = shell_exec($cmd);

        // if (preg_match('!^Input.+!sm', $meta, $matches)) {
        // extracts only the necessary metadata
        // if (preg_match('/Input/', $meta, $matches)) {
        //     // $meta = $matches[0];
        //     $input_pos = stripos($meta, 'Input');
        //     $meta = substr($meta , $input_pos );
        // } else {
        //     throw new Exception("Could not load video metadata.");
        // }

        if (preg_match('/\s*Duration: ((?:\d|:|\.)+)/', $meta, $matches, PREG_OFFSET_CAPTURE) )
            $meta = substr($meta , $matches[0][1] );
        else
            throw new \Exception("Could not load video metadata.");
        
        return $meta;
    }

    private function parseMetaData($meta) {
        $parsed = new \stdClass();
        
        # Store duration in seconds
        if (preg_match('/\s*Duration: ((?:\d|:|\.)+)/', $meta, $matches)) {
            $duration = explode(":", $matches[1]);
            $hours = (float) $duration[0];
            $minutes = (float) $duration[1];
            $seconds = (float) $duration[2] + (60 * $minutes) + (60 * 60 * $hours);
            
            $parsed->duration = $seconds;
        } else {
            throw new \Exception("Could not determine video's duration.");
        }
        //bitrate is not been user at the moment
        // if (preg_match('!bitrate: (\d+(?:\.\d+)?)!m', $meta, $matches)) {
        //     $parsed->bitrate = (float) $matches[1];
        // } else {
        //     throw new Exception("Could not determine video's bitrate.");
        // }
        
        $video_stream = self::findVideoStream($meta);
        $parsed->video_stream = $video_stream;

        //audio stream not in use for now
        // $audio_stream = self::findAudioStream($meta);
        // $parsed->audio_stream = $audio_stream;

        return $parsed;
    }

    public function verifyUpload() {
        // $formats = array('video/quicktime','video/mp4', 'video/h264');
        //
        // if (preg_match('{(\.\w+)$}', $this->original_video->file->getClientOriginalName(), $matches)) {
        //     $this->original_video->format = $matches[1];
        // } else {
        //     throw new Exception("Could not determine file format: ".$this->original_video->file->getClientOriginalName() );
        // }

        // if (!in_array($this->original_video->file->getClientMimeType(), $formats)) {
        //     throw new Exception("This file format is not allowed");
        // }

        // if ($this->original_video->file->getClientSize() > 1073741824){
        //     throw new Exception("This file format is too big. Uploads are limited to 1G");
        // }

        // $path = $this->video_dir.$this->title.$this->original_video->format;
        // if (!move_uploaded_file($this->original_video->file['tmp_name'], $path)) {
        //     throw new Exception("The file could not be moved. - $path");
        // }
        // $this->original_video->file->move($this->video_dir, $this->title.$this->original_video->format );

        // $this->original_video->path = $path;
        $path = $this->video->video_dir.$this->video->video_title.$this->video_type;
        $meta = $this->loadMetaData($path);

        // $this->parseMetaData($meta);
        // $this->original_video = new \stdClass();

        $this->metadata = new \stdClass();
         
        $this->metadata = $this->parseMetaData($meta);

        $this->duration = $this->metadata->duration;
        
        // if (stripos($this->original_video->metadata->video_stream->codec, "h264") === false) {
        //     unlink($path);
        //     throw new Exception("The video needs to have an h264 encoding.");
        // }
        // 
        // if ($this->original_video->metadata->video_stream->width < 960) {
        //     unlink($path);
        //     throw new Exception("Please upload a file that is at least 960px wide.");
        // }
    }
    
    public function convertOriginal() {
        $w = 960;
        $h = floor($w * ($this->metadata->video_stream->height / $this->metadata->video_stream->width));

        $cmd = "source ".$this->shell_scripts_dir."convert_video.sh '".$this->video->video_dir.$this->video->video_title.$this->video_type."' '".$this->video->video_title."' '".$w."x".$h."'";
        $outputfile = $this->video->video_dir.$this->video->video_title."_progress.txt";
        $pidfile = $this->video->video_dir.$this->video->video_title."_pid.txt";
        
        exec(sprintf("%s > %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
        
        $this->outputfile = $outputfile;
        $this->pidfile = $pidfile;
    }

    public function zipOriginal() {
        /*$zip = new ZipArchive();
        $file = $this->original_video->path.".zip";

        if ($zip->open($file, ZipArchive::CREATE)!==TRUE) {
            exit("cannot open <$file>\n");
        }

        $zip->addFile($this->original_video->path, "test.mov");
        $zip->close();*/
        // i need to implement this
        // exec("ditto -ck '".$this->original_video->path."' '".$this->original_video->path.".zip' 2>&1", $output, $return);
        // echo var_dump($output);
        // echo $return;
    }

    public function getId(){
        return $this->video->id;
    }
    public function getTitle() {
        return $this->video->video_title;
    }
    // public function getProject() {
    //     return $this->project;
    // }
    // public getReviewStage() {
    //     return $this->review_stage;
    // }
    // public getVersion() {
    //     return $this->version;
    // }
    public function getDescription() {
        return $this->video->description;
    }
    public function getDuration() {
        return $this->metadata->duration;
    }
    public function getVideoCodec() {
        return $this->metadata->video_stream->codec;
    }
    public function getWidth() {
        return $this->metadata->video_stream->width;
    }
    public function getHeight() {
        return $this->metadata->video_stream->height;
    }
    public function getFps() {
        return $this->metadata->video_stream->framerate;
    }
    // public function getAudioCodec() {
    //     return $this->metadata->audio_stream->codec;
    // }
    // public function getSampleRate() {
    //     return $this->metadata->audio_stream->sample_size;
    // }
    // public function getSampleSize() {
    //     return $this->metadata->audio_stream->sample_size;
    // }
    public function getVideoSrcs() {
        $this->video_srcs = $this->video->video_title.'.mp4, '.$this->video->video_title.'.webm';
        return $this->video_srcs;
    }
    public function getProgressFile() {
        return $this->outputfile;
    }
    public function getPidFile() {
        return $this->pidfile;
    }
    public function getTotalFrames() {
        if (isset($this->metadata)) {
            return floor(($this->metadata->duration) * ($this->metadata->video_stream->framerate));
        }
        return 0;
    }

}
?>
