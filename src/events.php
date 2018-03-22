<?php //-->
/**
 * This file is part of a Custom Package.
 */

use Cradle\Package\System\Schema\Validator;
use Cradle\Package\System\Schema;

use Cradle\Http\Request;
use Cradle\Http\Response;

/**
 * System Schema Create Elastic Mapping Job
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('system-schema-create-elastic', function ($request, $response) {
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    // check for required parameters
    if (!isset($data['name'])) {
        return $response->setError(true, 'Invalid parameters.');
    }

    // check if model exists
    $modelPath = sprintf($this->package('global')
            ->path('config') . '/schema/%s.php',
        $data['name']);

    if (!file_exists($modelPath)) {
        return $response->setError(true, 'Model doesn\'t exist.');
    }
    
    //----------------------------//
    // 3. Prepare Data
    //----------------------------//
    // get model data
    $data = include_once ($modelPath);
    
    //----------------------------//
    // 4. Process Data
    //----------------------------//
    $schema = Schema::i($data);
    $table = $schema->getName();

    //create elastic mappings
    $schema->service('elastic')->createMap();

    //return response format
    $response->setError(false)->setResults($data);
});

/**
 * System Schema Create Elastic Mapping Job
 *
 * @param Request $request
 * @param Response $response
 */
$this->on('system-schema-search-elastic', function ($request, $response) {
    
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //----------------------------//
    // 2. Validate Data
    //no validation needed
    //----------------------------//
    // 3. Prepare Data
    //no preparation needed
    //----------------------------//
    // 4. Process Data
    $path = $this->package('global')->path('config') . '/schema/elastic/';
    
    $files = @scandir($path);
    
    if (!$files) {
        return $response->setError(true, 'No elastic schemas found.');
    }

    $active = 1;
    if (isset($data['filter']['active'])) {
        $active = $data['filter']['active'];
    }
    
    $results = [];
    foreach ($files as $file) {
        if (trim($file) == '.'
            || trim($file) == '..'
        ) {
            continue;
        }

        if (file_exists(sprintf($path . '/%s/elastic.php', $file))) {
            
            $model = $this->package('global')
                ->config('admin/schema/%s.php', strtolower($file));

            $model['name'] = $model['singular'] = strtolower($file);
            $results[] = $model;
        }
        
    }
    
    //set response format
    $response->setError(false)->setResults([
        'rows' => $results,
        'total' => count($results)
    ]);
});

/**
 * System Schema Map Elastic
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('system-schema-map-elastic', function ($request, $response) {
    
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    //check for required parameters
    if (!isset($data['name'])) {
        return $response->setError(true, 'Invalid parameters.');
    }

    // check if model exists
    $modelPath = sprintf($this->package('global')
            ->path('config') . '/schema/%s.php',
        $data['name']);
        
    if (!file_exists($modelPath)) {
        return $response->setError(true, 'Model doesn\'t exist.');
    }

    //----------------------------//
    // 3. Prepare Data
    //----------------------------//
    
    //----------------------------//
    // 4. Process Data
    //----------------------------//
    $schema = Schema::i($data['name']);
    
    //map elastic
    

    //create elastic mappings
    $map = $schema->service('elastic')->map();
    
    //check if mapping is successfull
    if (!$map) {
        return $response->setError(true, 'Something went wrong.');
    }
    
    $response->setError(false);
});

/**
 * System Schema populate elastic
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('system-schema-populate-elastic', function($request, $response) {   
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    // check for required fields
    if (!isset($data['name'])) {
        return $response->setError(true, 'Invalid parameters.');
    }

    // check if model exists
    $modelPath = sprintf($this->package('global')
            ->path('config') . '/schema/%s.php',
        $data['name']);
        
    if (!file_exists($modelPath)) {
        return $response->setError(true, 'Model doesn\'t exist.');
    }

    //----------------------------//
    // 3. Prepare Data
    //----------------------------//
    
    //----------------------------//
    // 4. Process Data
    //----------------------------//
    $schema = Schema::i($data['name']);
    $populate = $schema->service('elastic')->populate($data);
    // check populate result
    if (!$populate) {
        return $response->setError(true, 'Something went wrong during elastic poulate');
    }
    
    $response->setError(false);
});

/**
 * System Schema populate elastic
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('system-schema-flush-elastic', function($request, $response) { 
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    // check for required fields
    if (!isset($data['name'])) {
        return $response->setError(true, 'Invalid parameters.');
    }

    // check if model exists
    $modelPath = sprintf($this->package('global')
            ->path('config') . '/schema/%s.php',
        $data['name']);
        
    if (!file_exists($modelPath)) {
        return $response->setError(true, 'Model doesn\'t exist.');
    }
    
    //----------------------------//
    // 3. Prepare Data
    //----------------------------//
    
    //----------------------------//
    // 4. Process Data
    //----------------------------//
    $schema = Schema::i($data['name']);
    $flush = $schema->service('elastic')->flush();

    // intercept error
    if (!$flush) {
        return $response->setError(true, 'Something went wrong.');
    }
    
    return $response->setError(false);
});

/**
 * System Schema get elastic schema
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('system-schema-get-elastic', function($request, $response) { 
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    // check for required fields
    if (!isset($data['name'])) {
        return $response->setError(true, 'Invalid parameters.');
    }

    // check if model exists
    $modelPath = sprintf($this->package('global')
            ->path('config') . '/schema/elastic/%s/elastic.php',
        ucwords($data['name']));
        
    if (!file_exists($modelPath)) {
        return $response->setError(true, 'Schema doesn\'t exist.');
    }
    
    //----------------------------//
    // 3. Prepare Data
    //----------------------------//
    
    //----------------------------//
    // 4. Process Data
    //----------------------------//
    $results = file_get_contents ($modelPath);

    return $response->setError(false)->setResults($results);
});


/**
 * System Schema update elastic schema
 *
 * @param Request $request
 * @param Response $response
 */
$cradle->on('system-schema-update-elastic', function($request, $response) {
    //----------------------------//
    // 1. Get Data
    $data = [];
    if ($request->hasStage()) {
        $data = $request->getStage();
    }

    // check for required fields
    if (!isset($data['name'])) {
        return $response->setError(true, 'Invalid parameters.');
    }

    // check if code is set
    if (!isset($data['code'])) {
        return $response->setError(true, 'Invalid parameters.');
    }

    // check if model exists
    $modelPath = sprintf($this->package('global')
            ->path('config') . '/schema/elastic/%s/elastic.php',
        ucwords($data['name']));

    // check if file exist
    if (!file_exists($modelPath)) {
        // return error if not
        return $response->setError(true, 'Schema doesn\'t exist.');
    }

    try {
        // save code
        file_put_contents ($modelPath, $data['code']);
    } catch (\Throwable $e) {
        return $response->setError(true, $e->getMessage());
    }
    
    return $response->setError(false);
});
