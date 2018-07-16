<?php

/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2018 JoomDev.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
defined('_JEXEC') or die;
jimport('astroid.framework.helper');
jimport('astroid.framework.template');

class AstroidElement {

   public $id = null;
   protected $app = null;
   public $type = '';
   public $title = '';
   public $icon = '';
   public $multiple = true;
   public $classname = '';
   public $description = '';
   protected $xml_file = null;
   protected $default_xml_file = null;
   protected $layout = null;
   public $params = [];
   public $data = [];
   protected $template = null;
   protected $xml = null;
   protected $form = null;

   public function __construct($type = '', $data = [], $template = null) {
      $this->type = $type;
      if (!empty($data)) {
         $this->id = $data['id'];
         $this->data = $data['params'];
      }
      $this->app = JFactory::getApplication();

      if ($template === null) {
         $template = $this->app->getTemplate(true);
         $this->template = new AstroidFrameworkTemplate($template);
      } else {
         $this->template = $template;
      }

      $this->setClassName();
      $this->template->setLog("Initiated Element : " . $type, "success");
      $library_elements_directory = JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'framework' . '/' . 'elements' . '/';
      $template_elements_directory = JPATH_SITE . '/' . 'templates' . '/' . $this->template->template . '/' . 'astroid' . '/' . 'elements' . '/';

      switch ($this->type) {
         case 'section':
            $this->default_xml_file = $library_elements_directory . 'section-default.xml';
            break;
         default:
            $this->default_xml_file = $library_elements_directory . 'default.xml';
            break;
      }

      if (file_exists($template_elements_directory . $this->type . '/' . $this->type . '.xml')) {
         $this->xml_file = $template_elements_directory . $this->type . '/' . $this->type . '.xml';
         $this->layout = $template_elements_directory . $this->type . '/' . $this->type . '.php';
      } else if (file_exists($library_elements_directory . $this->type . '/' . $this->type . '.xml')) {
         $this->xml_file = $library_elements_directory . $this->type . '/' . $this->type . '.xml';
         $this->layout = $library_elements_directory . $this->type . '/' . $this->type . '.php';
      }
      if ($this->xml_file !== null) {
         $this->loadXML();
      }
      $this->loadForm();
   }

   protected function setClassName() {
      $type = $this->type;
      $type = str_replace('-', ' ', $type);
      $type = str_replace('_', ' ', $type);
      $type = ucwords(strtolower($type));
      $classname = 'AstroidElement' . str_replace(' ', '', $type);
      $this->classname = $classname;
   }

   protected function loadXML() {
      $xml = simplexml_load_file($this->xml_file);
      $this->xml = $xml;
      $title = (string) @$xml->title;
      $icon = (string) @$xml->icon;
      $description = (string) @$xml->description;
      $color = (string) @$xml->color;
      $multiple = (string) @$xml->multiple;

      $this->title = $title;
      $this->icon = $icon;
      $this->description = $description;
      $this->color = $color;
      $this->multiple = $multiple == "false" ? false : true;
   }

   public function loadForm() {
      $this->form = new JForm($this->type);
      if (!empty($this->xml_file)) {
         $xml = $this->xml->form;
         $this->form->load($xml, false);
      }
      $defaultXml = simplexml_load_file($this->default_xml_file);
      $this->form->load($defaultXml->form, false);

      $formData = [];

      $fieldsets = $this->form->getFieldsets();
      foreach ($fieldsets as $key => $fieldset) {
         $fields = $this->form->getFieldset($key);
         foreach ($fields as $field) {
            $formData[] = ['name' => $field->name, 'value' => $field->value];
         }
      }

      $this->params = $formData;
   }

   public function getInfo() {
      return [
          'type' => $this->type,
          'title' => JText::_($this->title),
          'icon' => $this->icon,
          'description' => JText::_($this->description),
          'color' => $this->color,
          'multiple' => $this->multiple,
          'params' => $this->params,
      ];
   }

   public function renderForm() {
      $layout = new JLayoutFile('form', JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'framework' . '/' . 'layouts' . '/' . 'framework');
      $html = $layout->render(['element' => $this]);
      $form = str_replace(array("\n", "\r", "\t"), '', $html);
      $replacer = [
          'ng-show="' => 'ng-show="elementParams.',
          'ng-hide="' => 'ng-hide="elementParams.',
          'ng-model="' => 'ng-model="elementParams.',
          'ng-value="' => 'ng-value="elementParams.'
      ];

      foreach ($replacer as $find => $replace) {
         $form = str_replace($find, $replace, $form);
      }

      return $form;
   }

   public function getForm() {
      return $this->form;
   }

   protected function getParams() {
      $formData = [];
      $return = [];
      foreach ($this->data as $data) {
         $data = (array) $data;
         $formData[$data['name']] = $data['value'];
      }
      $params = [];
      foreach ($this->params as $param) {
         $param = (array) $param;
         if (isset($formData[$param['name']])) {
            $params[$param['name']] = $formData[$param['name']];
         } else {
            $params[$param['name']] = $param['value'];
         }
      }

      return $AstroidParams = new AstroidParams($params);
   }

   public function render() {
      if ($this->layout === null || !file_exists($this->layout)) {
         $this->template->setLog('Layout Not Found - Element : ' . $this->type);
         return '';
      }
      $pathinfo = pathinfo($this->layout);
      $layout = new JLayoutFile($pathinfo['filename'], $pathinfo['dirname']);

      $html = $layout->render(['params' => $this->getParams(), 'template' => $this->template, 'element' => $this]);

      $return = '';
      if (!empty($html)) {
         $return .= '<div class="' . $this->getClass() . '" id="' . $this->getID() . '" style="' . $this->getStyles() . '" data-animation="' . $this->getAnimation() . '" ' . $this->getAttributes() . '>' . $html . '</div>';
      }
      return $return;
   }

   public function renderWireframe() {
      $layout = new JLayoutFile('wireframe', JPATH_LIBRARIES . '/' . 'astroid' . '/' . 'framework' . '/' . 'layouts' . '/' . 'framework');
      $html = $layout->render(['params' => $this->getParams(), 'template' => $this->template, 'element' => $this]);
      return $html;
   }

   public function getID() {
      $params = $this->getParams();
      $customid = $params->get('customid', '');
      if (!empty($customid)) {
         return $customid;
      } else {
         return $this->template->slugify($params->get('title') . '-' . $this->id);
      }
   }

   public function getClass() {
      $params = $this->getParams();
      $classes = [];
      $classes[] = $this->template->slugify('astroid-' . $this->type);
      $customclass = $params->get('customclass', '');
      if (!empty($customclass)) {
         $classes[] = $customclass;
      }

      $hideonxs = $params->get('hideonxs', 0);
      if ($hideonxs) {
         $classes[] = 'hideonxs';
      }
      $hideonsm = $params->get('hideonsm', 0);
      if ($hideonsm) {
         $classes[] = 'hideonsm';
      }
      $hideonmd = $params->get('hideonmd', 0);
      if ($hideonmd) {
         $classes[] = 'hideonmd';
      }
      $hideonlg = $params->get('hideonlg', 0);
      if ($hideonlg) {
         $classes[] = 'hideonlg';
      }
      $hideonxl = $params->get('hideonxl', 0);
      if ($hideonxl) {
         $classes[] = 'hideonxl';
      }

      return implode(' ', $classes);
   }

   public function getAnimation() {
      $params = $this->getParams();
      $animation = $params->get('animation', '');
      return $animation;
   }

   public function getStyles() {
      $params = $this->getParams();
      $styles = [];
      $background = $params->get('background', 0);
      if ($background) {
         $background_color = $params->get('background_color', '');
         if (!empty($background_color)) {
            $styles[] = 'background-color:' . $background_color;
         }
         $background_image = $params->get('background_image', '');
         if (!empty($background_image)) {
            $styles[] = 'background-image: url(' . JURI::root() . 'images/' . $background_image . ')';
            $background_repeat = $params->get('background_repeat', '');
            $background_repeat = empty($background_repeat) ? 'inherit' : $background_repeat;
            $styles[] = 'background-repeat:' . $background_repeat;

            $background_size = $params->get('background_size', '');
            $background_size = empty($background_size) ? 'inherit' : $background_size;
            $styles[] = 'background-size:' . $background_size;

            $background_attchment = $params->get('background_attchment', '');
            $background_attchment = empty($background_attchment) ? 'inherit' : $background_attchment;
            $styles[] = 'background-attachment:' . $background_attchment;

            $background_position = $params->get('background_position', '');
            $background_position = empty($background_position) ? 'inherit' : $background_position;
            $styles[] = 'background-position:' . $background_position;
         }
      }
      return implode(';', $styles);
   }

   public function getSectionLayoutType() {
      $params = $this->getParams();
      $container = $params->get('layout_type', '');
      $custom_class = $params->get('custom_container_class', '');
      switch ($container) {
         case '':
            $container = 'container';
            break;
         case 'no-container':
            $container = '';
            break;
         case 'container-with-no-gutters':
            $container = 'container';
            break;
         case 'container-fluid-with-no-gutters':
            $container = 'container-fluid';
            break;
      }
      if (!empty($container) && !empty($custom_class)) {
         $container .= ' ' . $custom_class;
      }
      return $container;
   }

   public function getAttributes() {
      $params = $this->getParams();
      $attributes = [];

      $background = $params->get('background', 0);
      if ($background) {
         $background_video = $params->get('background_video', '');
         if (!empty($background_video)) {
            $attributes['data-jd-video-bg'] = JURI::root() . 'images/' . $background_video;
         }
      }

      $return = [];
      foreach ($attributes as $key => $value) {
         $return[] = $key . '="' . $value . '"';
      }
      return implode(' ', $return);
   }

   public function getValue($key, $default = '') {
      $params = $this->getParams();
      return $params->get($key, $default);
   }

}

class AstroidParams {

   public $params = [];

   function __construct($params) {
      $this->params = $params;
   }

   public function get($key, $default = null) {
      if (isset($this->params[$key])) {
         return $this->params[$key];
      } else {
         return $default;
      }
   }

}

?>