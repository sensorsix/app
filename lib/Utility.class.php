<?php

class Utility
{
  /**
   * @param Doctrine_Collection $collection
   * @param int $position position into collection
   * @param int $level
   * @return array
   */
  public static function collectionToTree(Doctrine_Collection $collection, &$position = 0, $level = 0)
  {
    $result = array();
    for ($i = $position; $i < $collection->count(); $i++)
    {
      if ($level < $collection[$i]->level)
      {
        $perv_index = $i - 1;
        $result[$collection[$perv_index]->id]['children'] = self::collectionToTree($collection, $i, $collection[$i]->level);
      }
      else
      {
        if ($level > $collection[$i]->level)
        {
          // Shift loop global index
          $position = $i - 1;
          return $result;
        }
        else
        {
          $result[$collection[$i]->id]['data'] = $collection[$i];
        }
      }
    }
    // Shift loop global index
    $position = $i - 1;
    return $result;
  }

  /**
   * Makes text teaser
   *
   * @param string $text
   * @param int $limit
   * @return string
   */
  public static function teaser($text, $limit = 100)
  {
    $teaser = '';
    $length = 0;

    // Split text by words trying to get a whitespace
    // character before each word.
    if (preg_match_all('/\s?\S+/u', $text, $matches))
    {
      foreach ($matches[0] as $word)
      {
        $w_length = mb_strlen($word, 'UTF-8');
        if ($length + $w_length <= $limit)
        {
          $teaser .= $word;
          $length += $w_length;
        }
        else
        {
          if ($length == 0)
          {
            // If even the first word is too long then cut it
            // to get something as result.
            $teaser = mb_substr($word, 0, $limit, 'UTF-8');
          }
          $teaser .= '...';

          break;
        }
      }
    }

    return $teaser;
  }

  /**
   * Sort an array based on another array
   *
   * @param array $array
   * @param array $order
   * @return array
   */
  public static function sortArrayByArray($array, $order)
  {
    $ordered = array();
    foreach ($order as $key)
    {
      if (array_key_exists($key, $array))
      {
        $ordered[$key] = $array[$key];
        unset($array[$key]);
      }
    }

    return $ordered + $array;
  }


  /**
   * Based on zero indexed numbers, meaning 0 == A, 1 == B, etc
   *
   * @param $num
   * @return string
   */
  public static function getNameFromNumber($num)
  {
      $numeric = $num % 26;
      $letter = chr(65 + $numeric);
      $num2 = intval($num / 26);
      if ($num2 > 0) {
          return self::getNameFromNumber($num2 - 1) . $letter;
      } else {
          return $letter;
      }
  }

  /**
   * @param $text
   * @param null $css_class
   * @param bool $shorten
   * @param bool $at_new_line
   * @return mixed
   */
  public static function tag_links($text, $css_class = NULL, $shorten = FALSE, $at_new_line = FALSE)
  {

    $url_pattern = '([\p{L}0-9_-]+(?:\.[\p{L}0-9_-]+)+)\S*\b';
    $text = preg_replace("/((www\.)$url_pattern)/u", "http://$1", $text);

    $text = preg_replace(
      "/(?:(?:http|ftp|https):\/\/)$url_pattern/u",
      sprintf(
        '%s<a href="$0" target="_blank"%s>$%d</a>',
        $at_new_line ? '<br/>' : '',
        $css_class === NULL ? '' : " class=\"$css_class\"",
        $shorten ? 1 : 0
      ),
      $text
    );

    return $text;
  }
}