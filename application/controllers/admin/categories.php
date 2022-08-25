<?php
    class Admin_Categories_Controller extends Admin_Controller
    {
        public function index()
        {
            $category = new Category_Model; 
            
            $data['categories'] = $category->get_all_special_and_shared_categories();
            $data['settings']   = $this->view->settings;
            
            $this->view->content = View::capture("categories" . DS . "categories_list", $data, true, array("settings"));
        }
        
        public function load_children_html($request_type = "", $id = false)
        {
            $this->is_ajax($request_type);
            
            if($id)
            { 
                $category = new Category_Model;
                $category = $category->find_by_id($id);
                
                if($category)
                { 
                    $category->find_subcategories();
                }
                
                if($category->subcategories)
                { 
                    $data['category'] = $category;
                    $this->view->html = View::capture("categories" . DS . "subcategories_list", $data);
                }
                else
                {
                    $data             = array();
                    $this->view->html = View::capture("categories" . DS . "no_subcategories", $data);
                }
            } 
            
            $this->ajax->result     = 'ok';
            $this->ajax->callback   = 'set_children_html_and_toggle';
            $this->ajax->data->html = $this->view->html;
            
            $this->ajax->render();
        }
        
        public function form($id = false)
        {
            $category = new Category_Model;
            
            $parent_categories = $category->get_all_special_and_shared_categories(); 
            
            $root_parent_category = new stdClass;
            
            // Adding root category to our list
            $root_parent_category->name                 = "Root";
            $root_parent_category->id                   = "0";
            $root_parent_category->show_in_modules = "Root";
            
            array_unshift($parent_categories, $root_parent_category);
            
            $data['parent_categories'] = $parent_categories;
            
            if($id)
            {
                $category = $category->find_by_id($id);
                
                if($category)
                {
                    $data['category']   = $category;
                    $data['action']     = "Edit";
                    $data['is_editing'] = true;
                }
                else
                {
                    $error = new Error_Controller;
                    $error->show_404();
                }
            }
            else
            {
                $data['category']   = $category;
                $data['action']     = 'Add new';
                $data['is_editing'] = false;
            }

            $data["settings"]    = $this->view->settings;
            $this->view->content = View::capture("categories" . DS . "category_form", $data, true, array("settings"));
        }
        
        public function save($request_type = "")
        {
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $category = new Category_Model;
            
            $category->bind($this->input->post('category'));
            $category->validate();
            
            $this->model_errors->ajaxify_if_has_errors();
            
            if(!$category->is_unique_name_in_siblings_list())
            {
                $value = "At this level already exists category with this name.";
                $this->model_errors->set("name_uniqness", $value);
            }
            
            $this->model_errors->ajaxify_if_has_errors();
            
            $category->if_is_subcategory_inherit_show_in_modules();
            
            if($category->save())
            {
                $this->session->set_modal_show_confirmation(); 
                
                $this->ajax->data->url_segments = 'categories/index';
                $this->ajax->result             = 'ok';
                $this->ajax->callback           = 'redirect';
                
                $this->ajax->render();
            } 
        }
        
        public function change_status($request_type = "", $id = 0)
        {
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $category = new Category_Model();
            
            $category = $category->find_by_id($id);
            
            if($category)
            {
                $category->change_status();
                
                if($category->save())
                {
                    $this->ajax->result       = 'ok';
                    $this->ajax->callback     = 'update_status';
                    $this->ajax->data->status = $category->status;
                    
                    $this->ajax->render();
                }
            }
        }

        public function delete_root_category($request_type = "",
                                                         $id           = 0)
        {
            $this->is_ajax($request_type);
            $this->validate_token();

            $category = new Category_Model();
            $category = $category->find_by_id($id);

            if($category)
            {
                if($category->delete_root_category())
                {
                    $this->ajax->result   = 'ok';
                    $this->ajax->callback = 'delete_categories';

                    $this->ajax->render();
                }
            }
        }

        public function delete_subcategory($request_type = "",
                                                      $id           = 0)
        {
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $category = new Category_Model();
            $category = $category->find_by_id($id);
            
            if($category)
            {
                if($category->delete_category())
                {
                    $this->ajax->result   = 'ok';
                    $this->ajax->callback = 'delete_categories';

                    $this->ajax->render();
                }
            }
        }
    }
?>