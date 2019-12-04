<?php

/** Controller for Reports
  * This sends out all data to the views that are used to
  * view the Reports
  **/

  class ReportCtrl extends Ctrl {
    protected $Auth;
    protected $User;
    public    $Errors;

    public function __construct($model, $controller, $action){
      parent::__construct($model, $controller, $action);
      $this->Errors = new Errors;
      $this->Auth = new Auth;

      $logged = false;
      if($this->Auth->isLoggedIn()){
        $this->User = $this->Auth->getProfile();
        $this->set('user', $this->User);
        $logged = true;
      }
      $this->set('logged', $logged);
    }

    /** Report List **/
    public function index(){
      if(!$this->Auth->isLoggedIn()){
        $logged = false;
      }
      else {
        $logged = true;
        $this->set('logged', $logged);
      }

      $this->set('title', 'Lista dei Report');
      $Errors = new Errors();

    }

    /** View Report - public **/
    public function view($id){ }

    /** New Report **/
    public function create(){

      if(!$this->Auth->isLoggedIn()){
        header('Location: /user/login?r=1');
      }
      else {

        $logged = true;
        $this->set('logged', $logged);

        $this->set('title', 'Nuovo Report');
        $Errors = new Errors();
        $this->set('street_map', true);
        $this->set('js', array('components/oc_api.js', 'components/leaflet_location_map.js'));

        $data = null;
        if( httpCheck('post', true) ){
          $data = $_POST;
          // $this->set('data', $data);

          $videos = $data['video-attachment'];
          $links = $data['link-attachment'];
          unset($data['video-attachment']);
          unset($data['link-attachment']);

          $data = array_filter($data);

          $data['created_by'] = $this->User->id;
          $report = $this->Report->create($data);

          if(is_numeric($report)){
            $this->Errors->set(21);

            // Upload Files
            if(!empty($_FILES)){
              $files = rearrange_files($_FILES['file-attachment']);

              $Files = new Meta('file_repository');
              $File = new Repo();

              $fileInfo = array('title' => 'Report File - ' . $report, 'file_type' => 2, 'disclosure' => 100, 'uid' => $this->User->id);
              $filelist = array();

              foreach($files as $i => $file){
                if($file['error'] == 0){
                  $filelist[] = $File->upload($file, $fileInfo);
                }
                else {
                  $this->Errors->set(650);
                }
              }

              if(count($filelist) > 0) {
                $f = $Files->updateFileReferences(T_REP_BASIC, $report, $filelist);
              }
              if(!$f instanceof Errors){
                $this->Errors->set(91);
              }
            }

            // Upload Links
            if(!empty($links)){
              $links = array_filter($links);
              $Links = new Meta('link_repository');
              $Link = new Link();
              $linkList = array();
              foreach($links as $link){
                $link_id = $Link->create(array('URL' => $link));
                $linkList[] = $link_id;
              }
              $f = $Links->updateReferences(T_REP_BASIC, $report, $linkList);
              if(!$f instanceof Errors){
                $this->Errors->set(92);
              }
            }

            // Upload Video Links
            if(!empty($videos)){
              $videos = array_filter($videos);
              $Videos = new Meta('video_repository');
              $Video  = new Video();
              $videoList = array();
              foreach($videos as $video){
                $video_id = $Video->create(array('URL' => $video));
                $videoList[] = $video_id;
              }
              $f = $Videos->updateReferences(T_REP_BASIC, $report, $videoList);
              if(!$f instanceof Errors){
                $this->Errors->set(93);
              }
            }
          }
          else {
            $this->Errors->set(551);
          }
        }
        $this->set('data', $data);
        $this->set('errors', $this->Errors);
      }
    }

    /** Edit Report **/
    public function edit($id){

      if(!$this->Auth->isLoggedIn()){
        header('Location: /user/login?r=1');
      }
      else {

        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        // Load Report
        $r = $this->Report->find($id);

        // Check for ownership or permissions
        if( hasPermission($this->User, array(P_EDIT_REPORT, P_ASSIGN_REPORT, P_BOUNCE_REPORT, P_COMMENT_REPORT, P_MANAGE_REPORT_CARD)) || $this->User->id == $r->created_by){


          $logged = true;
          $this->set('logged', $logged);

          $this->set('title', 'Modifica Report');
          $Errors = new Errors();
          $this->set('street_map', true);
          $this->set('js', array('components/oc_api.js', 'components/leaflet_location_map.js'));

          if( httpCheck('post', true) ){
            $data = $_POST;
            // $this->set('data', $data);

            $videos = $data['video-attachment'];
            $links = $data['link-attachment'];
            unset($data['video-attachment']);
            unset($data['link-attachment']);
            unset($data['id']);
            $update = $this->Report->update($id, $data);
            if($update) {
              $record = $id;
              $this->Errors->set(21);
            }
            if(!empty($_FILES)){
              $files = rearrange_files($_FILES['file-attachment']);

              $Files = new Meta('file_repository');
              $File = new Repo();

              $fileInfo = array('title' => 'Report File - ' . $record, 'file_type' => 2, 'disclosure' => 100, 'uid' => $this->User->id);
              $filelist = array();

              foreach($files as $i => $file){
                if($file['error'] == 0){
                  $filelist[] = $File->upload($file, $fileInfo);
                }
                else {
                  $this->Errors->set(650);
                }
              }

              if(count($filelist) > 0) {
                $f = $Files->updateFileReferences(T_REP_BASIC, $record, $filelist);
                if(!$f instanceof Errors){
                  $this->Errors->set(91);
                }
              }

            }
            // Upload Links
            if(!empty($links)){
              $links = array_filter($links);
              $Links = new Meta('link_repository');
              $Link = new Link();
              $linkList = array();
              foreach($links as $link){
                $link_id = $Link->create(array('URL' => $link));
                $linkList[] = $link_id;
              }
              $f = $Links->updateLinkReferences(T_REP_BASIC, $record, $linkList);
              if(!$f instanceof Errors){
                $this->Errors->set(92);
              }
            }

            // Upload Video Links
            if(!empty($videos)){
              $videos = array_filter($videos);
              $Videos = new Meta('video_repository');
              $Video  = new Video();
              $videoList = array();
              foreach($videos as $video){
                $video_id = $Video->create(array('URL' => $video));
                $videoList[] = $video_id;
              }
              $f = $Videos->updateVideoReferences(T_REP_BASIC, $record, $videoList);
              if(!$f instanceof Errors){
                $this->Errors->set(93);
              }
            }
            $this->set('errors', $this->Errors);
          }



          // Load Report
          $report = $this->Report->find($id);
          // Get Files
          $Files = new Repo();
          $report->files = $Files->getFiles(T_REP_BASIC, $id, 2);
          foreach($report->files as $i => $file){
            $report->files[$i]->info = $Files->getInfo(ROOT.DS.'public'.DS.'resources'.DS.$file->file_path);
          }

          // Get Links
          $LoadLinks = new Meta('link_repository');
          $LoadLink = new Link();
          $report->links = $LoadLinks->getRepoReference('link_repository', T_REP_BASIC, $id);

          // Get Videos
          $LoadVideos = new Meta('video_repository');
          $LoadVideo  = new Video();
          $Vids = $LoadVideos->getRepoReference('video_repository', T_REP_BASIC, $id);
          foreach($Vids as $i => $v){
            if(strpos($v->URL, 'youtube')){
              $v_pieces = explode('?v=', $v->URL);
              $v_id = array_pop($v_pieces);
              $Vids[$i]->embed = 'https://www.youtube.com/embed/' . $v_id;
            }
          }
          $report->videos = $Vids;



          $this->set('data', $report);

        } else {
          header('Location: /user/forbidden');
        }
      }
    }

    /** Delete **/
    public function delete($id){ }
  }
