<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2012     Pierrick LE GALL    http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

if( !defined("PHPWG_ROOT_PATH") )
{
  die ("Hacking attempt!");
}

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');
include_once(PHPWG_ROOT_PATH.'admin/include/functions_upload.inc.php');

$admin_base_url = get_root_url().'admin.php?page=plugin-properties_mass_update-update';

function ppmu_remove_utf8_bom($text)
{
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}

// +-----------------------------------------------------------------------+
// | Checks                                                                |
// +-----------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

$regex_for_separator = array(
  'tab' => '/^([^\t]+)\t+(.*)$/',
  'space' => '/^([^\s]+)\s+(.*)$/',
  'comma' => '/^([^,]+),+(.*)$/',
  'semicolon' => '/^([^;]+);+(.*)$/',
  );

if (isset($_POST['submit']))
{
  if (!in_array($_POST['separator'], array_keys($regex_for_separator)))
  {
    die('Hacking attempt!');
  }

  if (!in_array($_POST['property'], array('name', 'comment', 'author', 'tags')))
  {
    die('Hacking attempt!');
  }
}

// +-----------------------------------------------------------------------+
// | Actions                                                               |
// +-----------------------------------------------------------------------+

if (isset($_FILES) and !empty($_FILES['update']))
{
  $starttime = get_moment();
  
  if (UPLOAD_ERR_OK == $_FILES['update']['error'])
  {
    if (in_array($_FILES['update']['type'], array('text/plain', 'text/csv')))
    {
      $text_file = $_FILES['update']['tmp_name'];
    }
    else
    {
      array_push($page['errors'], l10n('Wrong file, please select a plain text file'));
    }

    if (isset($text_file))
    {
      ini_set("auto_detect_line_endings", true);
      $raw_lines = file($text_file);

      $raw_lines[0] = ppmu_remove_utf8_bom($raw_lines[0]);
      
      $query = 'SELECT id, file FROM '.IMAGES_TABLE.';';
      $existing_files = hash_from_query($query, 'file');
            
      $updates = array();
      $update_files = array();
      $missing_files = array();
      $tags_of = array();

      foreach ($raw_lines as $raw_line)
      {
        // finish to clean line endings
        $raw_line = trim($raw_line);

        if (!preg_match($regex_for_separator[$_POST['separator']], $raw_line, $matches))
        {
          continue;
        }

        // in case the same file is defined twice, we only save the first occurence
        if (isset($update_files[$matches[1]]) or isset($missing_files[$matches[1]]))
        {
          continue;
        }

        if (isset($existing_files[$matches[1]]))
        {
          $update_files[$matches[1]] = true;
          $image_id = $existing_files[$matches[1]]['id'];

          if ('tags' == $_POST['property'])
          {
            $tags_of[$image_id] = array();
            $raw_tags = explode(',', $matches[2]);
            foreach ($raw_tags as $tag)
            {
              $tag = trim($tag);
              if (empty($tag))
              {
                continue;
              }
              $tag_id = tag_id_from_tag_name(pwg_db_real_escape_string($tag));
              array_push($tags_of[$image_id], $tag_id);
            }
          }
          else
          {
            array_push(
              $updates,
              array(
                'id' => $image_id,
                $_POST['property'] => pwg_db_real_escape_string($matches[2]), // TODO right trim
                )
              );
          }
        }
        else
        {
          $missing_files[$matches[1]] = true;
        }
      }
      
      if ('tags' == $_POST['property'])
      {
        set_tags_of($tags_of);
      }
      else
      {
        mass_updates(
          IMAGES_TABLE,
          array(
            'primary' => array('id'),
            'update' => array($_POST['property']),
            ),
          $updates
          );
      }

      $endtime = get_moment();
      $elapsed = ($endtime - $starttime);

      array_push(
        $page['infos'],
        sprintf(
          l10n('%d photos updated'),
          count(array_keys($update_files))
          )
        );
      
      if (count($missing_files) > 0)
      {
        array_push(
          $page['errors'],
          sprintf(
            l10n('%d photos are missing in Piwigo: %s'),
            count($missing_files),
            implode(', ', array_keys($missing_files))
            )
          );
      }
    }
  }
  else
  {
    array_push($page['errors'], $_FILES['update']['error']);
  }
}

// +-----------------------------------------------------------------------+
// | form options                                                          |
// +-----------------------------------------------------------------------+

// image level options
$selected_level = isset($_POST['level']) ? $_POST['level'] : 0;
$template->assign(
    array(
      'level_options'=> get_privacy_level_options(),
      'level_options_selected' => array($selected_level)
    )
  );
?>