<?php

class opTimelinePluginUtil
{
  public static function screenNameReplace($body, $options = array())
  {
    preg_match_all('/(@+)([-._0-9A-Za-z]+)/', $body, $matches);
    if ($matches)
    {
      $i = 0;
      foreach ($matches[2] as $screenName)
      {
        $member = Doctrine::getTable('MemberConfig')->findOneByNameAndValue('op_screen_name', $screenName);
        
        if ($member)
        {
          $memberId = $member->getMemberId();
          $link = link_to('@'.$screenName, app_url_for('pc_frontend', array('sf_route' => 'obj_member_profile', 'id' => $memberId), true), array('target' => '_blank'));
          $mention = '/'.$matches[0][$i].'/';
          $body = preg_replace($mention, $link, $body);
        }

        $i++;
      }
    }
    return $body;
  }

  public static function hasScreenName($body)
  {
    preg_match_all('/(@+)([-._0-9A-Za-z]+)/', $body, $matches);
    if($matches[2])
    {
      $memberIds = array();
      foreach ($matches[2] as $screenName)
      {
        $member = Doctrine::getTable('MemberConfig')->findOneByNameAndValue('op_screen_name', $screenName);
        if ($member)
        {
          $memberIds[] = $member->getMemberId();
          $memberObject = Doctrine::getTable('Member')->find($member->getMemberId());
          opNotificationCenter::notify(sfContext::getInstance()->getUser()->getMember(), $memberObject, $body, array('category' => 'other', 'url' => url_for('@member_timeline?id='.sfContext::getInstance()->getUser()->getMemberId())));
        }
      }
      $memberId = implode("|", $memberIds);
      return '|' . $memberId . '|';
    }
    else
    {
      return null;
    }
  }
}
