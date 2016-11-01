<?php

namespace Resourceful;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class SchemaHandler
{		
    protected $schemaId;
    protected $template;
    protected $replacements;
	
	
	/**
	 * @return string schemaUrl
	 */
	public static function getSchemaUrl($schemaId, Application $app)
	{	
 		static $urlCache =array();
		
		assert (!empty($schemaId));
			
		if( !isset($urlCache[$schemaId])){
			$urlCache[$schemaId] = $app["url_generator"]->generate('schema', array("id" => $schemaId));
		}
		
		return $urlCache[$schemaId];
	}
	
	
	/**
	 * create a schema from template if it does not exist
	 *
	 * @return string schemaUrl
	 */	
	protected static function _register(Application $app, $schemaId, $template,  array $replacements)
	{
		assert( isset($app['schema.cache']));
    	assert( isset($app['data.store']));
		assert( isset($app['twig']));
		
		$schemaUrl = static::getSchemaUrl($schemaId, $app);
		$store = isset($app['schema.store'])?$app['schema.store']:$app['data.store'];

        if (
        	$app['createDefault']
        	&& !$store->contains($schemaUrl)
			&& ($schema=json_decode($app["twig"]->render("schema_{$template}.json.twig", $replacements)))
		) {
            $store->save($schemaUrl, $schema);
		}
		
        $app["schema.cache"]->add($schemaUrl, $store->fetch($schemaUrl));
		
		return $schemaUrl;		
	}
	

	public static function register(Application $app, $schemaId=null, $template=null,  array $replacements = array())
	{
		$handler = new static($schemaId, $template, $replacements);
		return static::_register($app, $handler->schemaId, $handler->template,  $handler->replacements);
	}
	
	
    public function __construct($schemaId, $template=null, array $replacements = array())
    {
    	assert (!empty($schemaId));
		
		$this->schemaId=$schemaId;
        $this->template = $template?:$this->schemaId;
        $this->replacements = array_merge(array("schemaId" => $this->schemaId, "schemaTitle" => ucfirst($this->schemaId)),$replacements);
    }
	
	
    public function __invoke(Request $request, Application $app)
    {
		static::_register($app, $this->schemaId, $this->template, $this->replacements);
    }
}
