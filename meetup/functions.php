<?php
$configs = include("../config.php");
include("../globalfunctions.php");

function makeMUApiReq($type="get", $query="", $variables=[], $headers=[]){
    $headers[] = "Authorization: Bearer {$_SESSION['mu_access_token_details']['access_token']}";
    if(isset($fields)){
        $headers[] = "Content-Type: application/json";
    }

    return makeApiReq($type,"https://api.meetup.com/gql",
        json_encode(['query' => $query, 'variables'=>$variables]), $headers);
}

function redirectToMULogin(){
    global $configs;
    session_unset();

    $authorization_url = "https://secure.meetup.com/oauth2/authorize?client_id={$configs['mu_api_key']}&response_type=code&redirect_uri={$configs['mu_redirect_uri']}";
    header("Location: " . $authorization_url);
}

function getAccessToken($AuthCode){
    global $configs;
    $token_request_params = [
        "client_id" => $configs['mu_api_key'],
        "client_secret" => $configs['mu_api_secret'],
        "grant_type" => "authorization_code",
        "redirect_uri" => $configs['mu_redirect_uri'],
        "code" => $AuthCode
    ];

    $response = makeApiReq(
        "post",
        "https://secure.meetup.com/oauth2/access",
        http_build_query($token_request_params)
    );

    if (!isset($response["access_token"])) {
        echo "Error obtaining access token";
    } else {
        $_SESSION['mu_access_token_details'] = $response;
        echo "Access token: ";
    }

    printVars($response);
    return $response;
}

function getNetworkNGroupInfos(){
    $query = <<<GRAPHQL
       query {
          self {
            adminProNetworks {
              id
              name
              urlname
            }
            memberships {
              pageInfo {
                hasNextPage
              }
              edges {
                node {
                  id
                  name
                  status
                  urlname
                  link
                }
              }
            }
          }
        }
    GRAPHQL;

    $response = makeMUApiReq("post",  $query);


    if(!isset($response['data'])){
        echo 'Error: ';
    }else{
        echo 'Successfully fetched the groups info';
        $_SESSION['mu_groups_details'] = $response['data']['self']['memberships']['edges'];
        $_SESSION['mu_network_details'] = $response['data']['self']['adminProNetworks'];
    }

    printVars($response);
    return $response;
}

function createEvent($evtData){
    $query = <<<QUERY
    mutation CreateEvent(\$input: CreateEventInput!) {
      createEvent(input: \$input) {
        event {
          id
          title
          status
          description
          dateTime
          duration
        }
        errors {
          message
          code
          field
        }
      }
    }
    QUERY;

    $response = makeMUApiReq("post", $query, [ 'input' => $evtData ]);

    if (isset($response['errors']) || !empty($response['data']['createEvent']['errors'])) {
        printError('Error creating event:', $response) ;
    }

    return $response;
}

function fetchEvents($groupId){
    $query = <<<GRAPHQL
    {
      group(id: "$groupId") {
        id
        unifiedEvents {
          count
          edges {
            node {
              id
              title
              status
              description
              dateTime
              duration
              eventUrl
              images{
                id
                baseUrl
                preview
              }
              imageUrl
              status
              timeStatus
              endTime
              createdAt
            }
          }
        }
      }
    }
    GRAPHQL;

    $response = makeMUApiReq("post", $query, ['groupId' => $groupId]);

    if(!isset($response['data']['group']['unifiedEvents'])){
        printError("Error Fetching events", $response);
    }

    return $response['data']['group'];
}
function deleteEvent($eventId){
    $query = <<<GRAPHQL
        mutation(\$input: DeleteEventInput!) {
            deleteEvent(input: \$input) {
                success
                errors {
                  message
                  code
                  field
                }
            }
        }
    GRAPHQL;

    $response = makeMUApiReq("post", $query, [ "input" => [
        "eventId" => "$eventId",
        "removeFromCalendar" => true,
        "updateSeries" => true
    ]]);

    if(isset($response['deleteEvent']['errors'])) {
        printError("Error deleting event.", $response);
    }

    return $response;
}
function deleteAllEvents(){
    $eventDetails = fetchEvents($_SESSION['mu_groups_details'][0]['node']['id']);
    $events = $eventDetails['unifiedEvents']['edges'];
    if(empty($events)){
        printError("No Events to delete");
    }

    foreach ($events as $event){
        $deleteResponse = deleteEvent($event['node']['id']);
        printVars($deleteResponse);
    }
}

function updateEvent($newEventData){
    $query = <<<'QUERY'
        mutation updateEvent($input: EditEventInput!) {
            editEvent(input: $input) {
                event {
                    id
                    title
                    description
                    dateTime
                    duration 
                }
                errors {
                  message
                  code
                  field
                }
            }
        }
    QUERY;

    $response = makeMUApiReq("post", $query, ['input' => $newEventData, ]);

    // Check for errors
    if (isset($response['errors']) || !empty($response['data']['editEvent']['errors'])) {
        printError('Error updating event:', $response) ;
    }

    return $response;
}