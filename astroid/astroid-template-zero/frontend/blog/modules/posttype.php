<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2019 JoomDev.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */
// No direct access.
defined('_JEXEC') or die;
extract($displayData);

$article = $params['article'];
$params = $article->params;

$type = $params->get('astroid_article_type', 'regular');
$icon = '';
switch ($type) {
   case 'video':
      $type = $params->get('astroid_article_video_type', 'youtube');
      $icon = 'fab fa-' . $type;
      break;
   case 'gallery':
      $icon = 'far fa-images';
      break;
   case 'audio':
      $type = $params->get('astroid_article_audio_source', 'soundcloud');
      $icon = 'fab fa-' . $type;
      break;
   case 'review':
      $icon = 'far fa-star';
      break;
   case 'quote':
      $icon = 'fa fa-quote-left';
      break;
}
?>
<?php if (!empty($icon)) { ?>
   <dd class="article-post-type">
      <span class="article-icon article-icon-<?php echo $type; ?>"><?php echo!empty($icon) ? '<i class="' . $icon . '"></i>' : ''; ?></span>
   </dd>
<?php } ?>