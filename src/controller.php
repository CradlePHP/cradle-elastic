<?php //-->
/**
 * This file is part of a Custom Package.
 */

// Back End Controllers

/**
 * Render the Model Search Page
 *
 * @param Request $request
 * @param Response $response
 */
$this->get('/admin/system/schema/elastic/search', function ($request, $response) {
    //----------------------------//
    // 1. Prepare Data
    // no data to preapre
    //----------------------------//
    // 2. Process Request
    $this->trigger('system-schema-search-elastic', $request, $response);

    //if we only want the raw data
    if($request->getStage('render') === 'false') {
        return;
    }

    $data = $response->getResults();

    //----------------------------//
    // 3. Render Template
    $class = 'page-admin-system-schema-search page-admin';
    $data['title'] = cradle('global')->translate('System Elastic Schema');
    
    //render the body
    $body = $this
        ->package('cradlephp/cradle-elastic')
        ->template('search', $data, [
            'styles',
            'templates',
            'scripts',
            'row',
            'types',
            'lists',
            'details',
            'validation',
            'update',
            'options_type',
            'options_format',
            'options_validation',
            'options_icon'
        ]);

    //set content
    $response
        ->setPage('title', $data['title'])
        ->setPage('class', $class)
        ->setContent($body);

    //if we only want the body
    if($request->getStage('render') === 'body') {
        return;
    }
    
    //render page
    $this->trigger('admin-render-page', $request, $response);
});

/**
 * Create elastic schema
 *
 * @param Request $request
 * @param Response $response
 */
$this->get('/admin/system/schema/elastic/create/:name', function ($request, $response) {
    //----------------------------//
    // 1. Prepare Data
    // no data to preapre
    //----------------------------//
    // 2. Process Request
    //----------------------------//
    // trigger create elastic schema event
    $this->trigger('system-schema-create-elastic', $request, $response);

    $nextUrl = '/admin/system/schema/elastic/search';
    // check if there are errors
    if ($response->isError()) {
        $this->package('global')->flash($response->getMessage(), 'error');
        $this->package('global')->redirect($nextUrl);
    }

    // process is successfull
    $this->package('global')
        ->flash(sprintf('Elastic schema for %s generated successfully.',
            $request->getStage('name')), 'success');
    
    $this->package('global')->redirect($nextUrl);
});

/**
 * Create elastic schema
 *
 * @param Request $request
 * @param Response $response
 */
$this->get('/admin/system/schema/elastic/map/:name', function ($request, $response) {
    //----------------------------//
    // 1. Prepare Data
    // no data to preapre
    //----------------------------//
    // 2. Process Request
    //----------------------------//
    // redirect url
    $nextUrl = '/admin/system/schema/elastic/search';
    // trigger map elastic schema event
    $this->trigger('system-schema-map-elastic', $request, $response);
    // intercept errors
    if ($response->isError()) {
        $this->package('global')->flash($response->getMessage(), 'error');
        $this->package('global')->redirect($nextUrl);
    }

    $this->package('global')
        ->flash(sprintf('%s mapped successfully', ucwords ($request->getStage('name'))), 'success');
    
    $this->package('global')->redirect($nextUrl);
});

/**
 * Populate elastic schema
 *
 * @param Request $request
 * @param Response $response
 */
$this->get('/admin/system/schema/elastic/populate/:name', function ($request, $response) {
    //----------------------------//
    // 1. Prepare Data
    // no data to preapre
    //----------------------------//
    // 2. Process Request
    //----------------------------//
    // trigger elastic populate
    $nextUrl = '/admin/system/schema/elastic/search';
    $this->trigger('system-schema-populate-elastic', $request, $response);
    // intercept error
    if ($response->isError()) {
        $this->package('global')->flash($response->getMessage(), 'error');
        $this->package('global')->redirect($nextUrl);
    }

    $this->package('global')
        ->flash(sprintf('Successully populated %s', $request->getStage('name')), 'success');
    
    $this->package('global')->redirect($nextUrl);
});

/**
 * Flush elastic schema
 *
 * @param Request $request
 * @param Response $response
 */
$this->get('/admin/system/schema/elastic/flush/:name', function ($request, $response) {
    //----------------------------//
    // 1. Prepare Data
    // no data to preapre
    //----------------------------//
    // 2. Process Request
    // trigger elastic flush
    $nextUrl = '/admin/system/schema/elastic/search';
    $this->trigger('system-schema-flush-elastic', $request, $response);
    // intercept error
    if ($response->isError()) {
        $this->package('global')->flash($response->getMessage(), 'error');
        $this->package('global')->redirect($nextUrl);
    }
    
    $this->package('global')
        ->flash(sprintf('Successfully flushed %s.', $request->getStage('name')), 'success');
    
    $this->package('global')->redirect($nextUrl);
});


/**
 * Edit elastic schema
 *
 * @param Request $request
 * @param Response $response
 */
$this->get('/admin/system/schema/elastic/edit/:name', function ($request, $response) {
    //----------------------------//
    // 1. Prepare Data
    $data = [];
    
    //----------------------------//
    // 2. Process Request
    //----------------------------//
    $this->trigger('system-schema-get-elastic', $request, $response);
    // intercept error
    if ($response->isError()) {
        $this->package('global')->flash($response->getMessage(), 'error');
        $this->package('global')->redirect('/admin/system/schema/elastic/search');
    }

    $data['code'] = $response->getResults();
    // 3. Render Template
    $class = 'page-admin-system-schema-search page-admin';
    $data['title'] = 'Profile Elastic Schema';
    
    //render the body
    $body = $this
        ->package('cradlephp/cradle-elastic')
        ->template('form', $data, [
            'styles',
            'templates',
            'scripts',
            'row',
            'types',
            'lists',
            'details',
            'validation',
            'update',
            'options_type',
            'options_format',
            'options_validation',
            'options_icon'
        ]);

    //set content
    $response
        ->setPage('title', $data['title'])
        ->setPage('class', $class)
        ->setContent($body);
    
    //if we only want the body
    if($request->getStage('render') === 'body') {
        return;
    }
    
    //render page
    $this->trigger('admin-render-page', $request, $response);
});


/**
 * Edit elastic schema
 *
 * @param Request $request
 * @param Response $response
 */
$this->post('/admin/system/schema/elastic/edit/:name', function ($request, $response) {
    //----------------------------//
    // 1. Prepare Data
    $nextUrl = '/admin/system/schema/elastic/search';
    
    //----------------------------//
    // 2. Process Request
    // trigger update elastic schema
    $this->trigger('system-schema-update-elastic', $request, $response);
    // intercept error
    if ($response->isError()) {
        $this->package('global')->flash($response->getMessage(), 'error');
        $this->package('global')->redirect($nextUrl);
    }

    $this->package('global')
        ->flash(sprintf('Elastic schema %s', $request->getStage('name')), 'success');
    
    $this->package('global')->redirect($nextUrl);
});


