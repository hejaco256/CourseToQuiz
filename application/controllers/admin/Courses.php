<?php
class Courses extends Base
{

 /****** Middlewares ********/

   use checkAuth;


/****** Constructor ********/

  function __construct()
  {
    parent::__construct();

    // Load Models
    $this->load->model('courses_model');
    $this->load->helper('common');
  }

  function index(){
    $this->page([
      'section' => 'list',
      'page' => 'admin/courses',
      'list' => $this->courses_model->getAllCourses()
    ]);
  }


  /****** Update Course ********/

  function update_course(){
    if($this->input->post('update')){
      if($this->input->post('image_link_external')){
         $image_link = $this->input->post('image_link_external');
           $is_google_autoload = 0;
      }elseif($_FILES['image_link_raw']['name']){
           $config['upload_path']          = './assets/img/course/';
           $config['allowed_types']        = 'gif|jpg|png';
           $config['max_size']             = 100;
           $config['max_width']            = 1024;
           $config['max_height']           = 768;
           $this->load->library('upload', $config);
           $file = $this->upload->do_upload('image_link_raw');
           if( ! $file){
             $this->uri->segment('alert','<div class="alert alert-danger">'.$this->upload->display_errors().'</div>');
           }else{
             $upload = $this->upload->data();
             $config_manip = array(
                  'image_library' => 'gd2',
                  'source_image' => $upload['full_path'],
                  'new_image' => $upload['file_path'],
                  'maintain_ratio' => TRUE,
                  'create_thumb' => TRUE,
                  'thumb_marker' => '_thumb',
                  'width' => 225,
                  'height' => 225
              );
              $file_name = $upload['file_name'];
              $this->load->library('image_lib', $config_manip);
              if (!$this->image_lib->resize()) {
                   $this->uri->segment('alert','<div class="alert alert-danger">'.$this->image_lib->display_errors().'</div>');
              }else{
                 $thumbnail = $upload['raw_name'].'_thumb'.$upload['file_ext'];
                 $image_link = base_url() . 'assets/img/course/' . $thumbnail;
              }
              // clear //
              $this->image_lib->clear();
           }
             $is_google_autoload = 0;
      }elseif($this->input->post('image_google_autoload') == "on"){
            $is_google_autoload = 1;
            $image_link = NULL;
      }
        $updateData = [
          'course_title' => $this->input->post('title'),
          'course_name'  => $this->input->post('name'),
          'category_id'  => $this->input->post('category_id'),
          'updated'      => date('d-m-Y h:i:s'),
          'status'       => $this->input->post('status')
        ];
        if(isset($image_link)){
          $updateData['image_link'] = $image_link;
        }
        if($is_google_autoload == "on"){
          $updateData['is_google_autoload'] = 1;
        }else{
          $updateData['is_google_autoload'] = 0;
        }
        $output = $this->update('course',['id' => $this->uri->segment(4)],$updateData);
        if($output == true){
           $this->session->set_flashdata('alert','<div class="alert alert-success">Category Updated</div>');
        }
    }
    $this->page([
      'section' => 'update',
      'page' => 'admin/courses',
      'one' => $this->courses_model->oneCourse($this->uri->segment(4)),
      'list' => $this->all('category')
    ]);
  }

  /****** Create Course ********/

  function create_course(){
    if($this->input->post('create')){
          if($this->input->post('image_link_external')){
             $image_link = $this->input->post('image_link_external');
               $is_google_autoload = 0;
          }elseif($_FILES['image_link_raw']['name']){
               $config['upload_path']          = './assets/img/course/';
               $config['allowed_types']        = 'gif|jpg|png';
               $config['max_size']             = 100;
               $config['max_width']            = 1024;
               $config['max_height']           = 768;
               $this->load->library('upload', $config);
               $file = $this->upload->do_upload('image_link_raw');
               if( ! $file){
                 $this->uri->segment('alert','<div class="alert alert-danger">'.$this->upload->display_errors().'</div>');
               }else{
                 $upload = $this->upload->data();
                 $config_manip = array(
                      'image_library' => 'gd2',
                      'source_image' => $upload['full_path'],
                      'new_image' => $upload['file_path'],
                      'maintain_ratio' => FALSE,
                      'x_axis' => 225,
                      'y_axis' => 225,
                      'create_thumb' => TRUE,
                      'thumb_marker' => '_thumb'
                      // 'width' => 225,
                      // 'height' => 225
                  );
                  $file_name = $upload['file_name'];
                  $this->load->library('image_lib', $config_manip);
                  if (!$this->image_lib->resize()) {
                       $this->uri->segment('alert','<div class="alert alert-danger">'.$this->image_lib->display_errors().'</div>');
                  }else{
                     $thumbnail = $upload['raw_name'].'_thumb'.$upload['file_ext'];
                     $image_link = base_url() . 'assets/img/course/' . $thumbnail;
                  }
                  // clear //
                  $this->image_lib->clear();
               }
                 $is_google_autoload = 0;
          }elseif($this->input->post('image_google_autoload') == "on"){
                $is_google_autoload = 1;
                $image_link = NULL;
          }
        $output = $this->create('course',[
          'course_title' => $this->input->post('title'),
          'course_name'  => $this->input->post('name'),
          'category_id'  => $this->input->post('category_id'),
          'image_link'   => $image_link,
          'is_google_autoload' => $is_google_autoload,
          'created'      =>date('d-m-Y h:i:s'),
          'status'       => $this->input->post('status')
        ]);
        if($output == true){
           $this->session->set_flashdata('alert','<div class="alert alert-success">Course Created</div>');
        }
        redirect('admin/courses');
    }
    $this->page([
      'section' => 'create',
      'page' => 'admin/courses',
      'list' => $this->all('category')
    ]);
  }

  /****** Remove Course ********/

  function delete(){
       $this->remove('course',['id' => $this->uri->segment(4)]);
       redirect('admin/courses');
  }

}
?>
