<?php

/**
 * API Request Handler
 * 
 * This file handles all API requests and routes them to the appropriate functions
 */

function handleApiRequest($endpoint) {
    // Remove the /api prefix and get the clean endpoint path
    $endpoint = ltrim(str_replace('/api', '', $endpoint), '/');
    
    // Handle different API endpoints
    switch ($endpoint) {
        case '':
            // API root - documentation or available endpoints
            return [
                'message' => 'Welcome to the FastScore API', // Message to welcome users to the API
                'version' => '1.0', // Version of the API
                'endpoints' => [ // List of available endpoints
                    '/api/matches' => 'Get match data', // Endpoint to get match data
                    '/api/news' => 'Get news articles', // Endpoint to get news articles
                    '/api/teams' => 'Get team information', // Endpoint to get team information
                    '/api/User' => 'Get team information for user' // Endpoint to get team information for a user
                ]
            ];
            
        case 'matches':
            // Call function to get match data
            return getMatchesData();
            
        case 'news':
            // Call function to get news data
            return getNewsData();
            
        case 'teams':
            // Call function to get teams data
            return getTeamsData();

        case 'User':
            // Call function to get teams data for a user
            return getTeamsDataForUser();
            
        default:
            // If endpoint is not found, return a 404 error
            http_response_code(404);
            return ['error' => 'API endpoint not found', 'requested_endpoint' => $endpoint];
    }
}

/**
 * Get match data for the API
 */
function getMatchesData() {
    global $conn; // Use the global database connection
    
    try {
        // Prepare and execute SQL statement to get match data
        $stmt = $conn->prepare("SELECT league_name, team_home, team_away, score_home, score_away, match_time FROM match_data ORDER BY match_time DESC");
        $stmt->execute();
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all match data as an associative array
        
        return [
            'success' => true, // Indicate success
            'count' => count($matches), // Count of matches returned
            'data' => $matches // Match data
        ];
    } catch (Exception $e) {
        // If an error occurs, return a 500 error and the error message
        http_response_code(500);
        return [
            'success' => false, // Indicate failure
            'error' => 'Failed to fetch match data', // Error message
            'message' => $e->getMessage() // Exception message
        ];
    }
}

/**
 * Get news data for the API
 */
function getNewsData() {
    global $conn; // Use the global database connection
    
    try {
        // Prepare and execute SQL statement to get news data
        $stmt = $conn->prepare("SELECT title, text, content FROM news_data WHERE news_data.content IS NOT NULL");
        $stmt->execute();
        $news = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all news data as an associative array
        
        return [
            'success' => true, // Indicate success
            'count' => count($news), // Count of news articles returned
            'data' => $news // News data
        ];
    } catch (Exception $e) {
        // If an error occurs, return a 500 error and the error message
        http_response_code(500);
        return [
            'success' => false, // Indicate failure
            'error' => 'Failed to fetch news data', // Error message
            'message' => $e->getMessage() // Exception message
        ];
    }
}

/**
 * Get teams data for the API
 */
function getTeamsData() {
    global $conn; // Use the global database connection
    
    try {
        // Prepare and execute SQL statement to get teams data
        $stmt = $conn->prepare("SELECT * FROM team_info where team_info.club_name is not null"); // Query to get team information
        $stmt->execute();
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all teams data as an associative array
        
        return [
            'success' => true, // Indicate success
            'count' => count($teams), // Count of teams returned
            'data' => $teams // Teams data
        ];
    } catch (Exception $e) {
        // If an error occurs, return a 500 error and the error message
        http_response_code(500);
        return [
            'success' => false, // Indicate failure
            'error' => 'Failed to fetch team data', // Error message
            'message' => $e->getMessage() // Exception message
        ];
    }
} 

/**
 * Get teams data for the API for User
 */
function getTeamsDataForUser() {
    global $conn; // Use the global database connection
    
    try {
        // Prepare and execute SQL statement to get user-specific teams data
        $stmt = $conn->prepare("SELECT * FROM User where User.username is not null"); // Query to get user information
        $stmt->execute();
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all user-specific teams data as an associative array
        
        return [
            'success' => true, // Indicate success
            'count' => count($teams), // Count of user-specific teams returned
            'data' => $teams // User-specific teams data
        ];
    } catch (Exception $e) {
        // If an error occurs, return a 500 error and the error message
        http_response_code(500);
        return [
            'success' => false, // Indicate failure
            'error' => 'Failed to fetch team data', // Error message
            'message' => $e->getMessage() // Exception message
        ];
    }
} 