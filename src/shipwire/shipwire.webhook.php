<?php
namespace CharityRoot;

class ShipwireWebhook extends ShipwireResource
{
   protected function _buildRequestBody()
   {
      $json = [];

      if ($this->exists('topic')) {
         $json['topic'] = $this->get('topic');
      }

      if ($this->exists('url')) {
         $json['url'] = $this->get('url');
      }

      $this->_request_body = $json;
   }
}
