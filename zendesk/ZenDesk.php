<?php
/**
 * Zendesk Extension for Yii
 * Developed by: aCodeSmith.com
 */

class ZenDesk extends CApplicationComponent
{
    public $token;
    public $email;
    public $subdomain='';
    public $baseUrl;
    public $zenDeskUrl;
    public static $statuses = array(
        'New', 'Open', 'Pending', 'Solved', 'Closed'
    );

    public function init()
    {
        if(empty($this->baseUrl))
            $this->baseUrl = 'https://'.$this->subdomain.'.zendesk.com/api/v2';

        if(empty($this->zenDeskUrl))
            $this->zenDeskUrl = 'https://'.$this->subdomain.'.zendesk.com';
    }

    public function tickets($action='')
    {
        if(!empty($action))
            $action='/'.$action;

        return $this->curlWrap("/tickets".$action.".json");
    }

    public function getTicket($id)
    {
        return $this->curlWrap('/tickets/'.$id.'.json');
    }

    public function users($action='')
    {
        if(!empty($action))
            $action='/'.$action;

        return $this->curlWrap("/users".$action.".json");
    }

    public function getUserLink($user_id)
    {
        return $this->zenDeskUrl.'/agent/#/users/'.$user_id.'/tickets';
    }

    public function search($criteria,$params='')
    {
        $param_string='';
        if(!empty($params))
        {
            $params = is_string($params) ? $params[$params] : $params;
            foreach($params as $param)
            {
                $param_string.='&'.$param;
            }
        }

        return $this->curlWrap('/search.json?query='.$criteria.$param_string);
    }

    public function findTicketsByEmailCriteria($emails)
    {
        $emails = is_string($emails) ? explode('%%%%',$emails) : $emails;
        $query = '';

        foreach($emails as $email)
        {
            $query.='%20requester:'.$email;
        }

        return 'type:ticket'.$query;

    }

    public function findUserByEmailCriteria($emails)
    {
        $emails = is_string($emails) ? explode('%%%%',$emails) : $emails;
        $query = '';
        $count = 0;

        foreach($emails as $email)
        {
            $count++;
            $query.='email:'.$email.($count > 1 ? '%20':'');
        }

        return  $query.'%20type:user';
    }

    public function curlWrap($url, $json=null, $action='GET')
    {
    	$ch = curl_init();
        //throwing error due to php settings
    	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    	curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
    	curl_setopt($ch, CURLOPT_URL, $this->baseUrl.$url);
    	curl_setopt($ch, CURLOPT_USERPWD, $this->email."/token:".$this->token);

    	switch($action){
    		case "POST":
    			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    			break;
    		case "GET":
    			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    			break;
    		case "PUT":
    			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    			break;
    		case "DELETE":
    			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    			break;
    		default:
    			break;
    	}

    	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    	curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    	$output = curl_exec($ch);
    	curl_close($ch);
    	$decoded = json_decode($output);
    	return $decoded;
    }

    public function separateTicketsByStatus($tickets)
    {
        $groups = array_flip(self::$statuses);
        $results = array();

        foreach($tickets as $ticket)
        {
            if(!empty($ticket->status))
            {
                foreach($groups as $name=>$array)
                {
                    if($ticket->status==strtolower($name)
                    && empty($groups[$ticket->status][$ticket->id]))
                    {
                        $results[$ticket->status][$ticket->id]=$ticket;
                    }
                }
            }
        }

        return $results;
    }
}