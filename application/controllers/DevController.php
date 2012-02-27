<?php


class DevController extends Zend_Controller_Action {
	
	/**
	 * EntityManager
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;
	private $driver; //Database driver
	private $cmf; //Class metadata factory
	private $generatedEntityOutputDirectory; //Dir where to output new entity classes
	private $entityNamespace; //Entity folder
	private $config;
	
	public function init() {
		$this->_helper->disableView();
		if(APPLICATION_ENV != 'development'){
			die('-');
		}
		
		set_include_path(implode(PATH_SEPARATOR, array(
		    realpath(APPLICATION_PATH. '/dev/GeneratedEntities'),
		    get_include_path(),
		)));
		
		
		$this->config = Zend_Registry::get('config');
		$this->em = $this->_helper->getEntityManager();
    }
    
    private function initializeEntityGenerator(){
		$classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
		$classLoader->register();

		// config
		$config = new \Doctrine\ORM\Configuration();
		$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver(APPLICATION_PATH . '/dev/GeneratedEntities')); //??
		$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
		$config->setProxyDir(__DIR__ . '/Proxies');
		$config->setProxyNamespace('Proxies');
		

		
		//Register mappings for sets and enums
		$this->em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('set', 'string');
		$this->em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
		
		//Get database driver
		$this->driver = new \Doctrine\ORM\Mapping\Driver\DatabaseDriver(
		    $this->em->getConnection()->getSchemaManager()
		);
		$this->em->getConfiguration()->setMetadataDriverImpl($this->driver);
		
		//Get class metadata factory
		$this->cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory($this->em);
		$this->cmf->setEntityManager($this->em);
		
		$this->entityNamespace = 'Entity';
		$this->generatedEntityOutputDirectory = APPLICATION_PATH . '/dev/GeneratedEntities/'; 	
    }

    public function indexAction() {
    	echo '<a href="/dev/entities">Entity Generator</a><br>';
    }
    
    public function entitiesAction() {
 		$this->initializeEntityGenerator();
 		
		$classNames = $this->driver->getAllClassNames();
		
		echo '<form action="/dev/generate-entities" method="post">';
			echo '<p>';
				echo '<table width="400" cellpadding="4">';
					echo '<tr>
							<th colspan="2">Seaded</th>
						</tr>';
					echo '<tr>
							<td>Vanemklass:</td>
							<td>
								<select name="parentClassName">
									<option value="">-Puudub-</option>
									<option value="AbstractEntity">AbstractEntity</option>
								</select>
							</td>
						</tr>';
				echo '</table>';
			echo '</p>';
			echo '<p>';	
				echo '<table width="800" cellpadding="4">
				
						<tr>
							<th>Nimi</th>
							<th>Kas olemas</th>
							<th width="20">Genereeri</th>
						</tr>';
				
					foreach($classNames as $className){
						$filePath = $this->generatedEntityOutputDirectory. '/' . $this->entityNamespace . '/' . $className . '.php';
						echo '<tr>
								<td><u>' . $className . '</u></td>
								<td>' . ((file_exists($filePath))?$filePath:' - ') . '</td>
								<td><input type="checkbox" name="generate[]" value="' . $className . '"></td>
							</tr>';	
					}
		
					echo '<tr><td colspan="2"><input type="submit" value="Genereeri klassid"></td></tr>';
					
				echo '</table>';
			echo '</p>';
		echo '</form>';
	
    }   
    
    public function generateEntitiesAction(){
    	$this->initializeEntityGenerator();
    	
    	if($this->_request->isPost()){
    		
    		$metadatas = array();
    		
    		$this->cmf->getAllMetadata(); //required for caching metadatas (maybe?)
    		
    		foreach($this->_getParam('generate') as $className){
    			if(in_array($className, $this->driver->getAllClassNames())){
    				$metadata =  $this->cmf->getMetadataFor($className);
    				$metadata->name = $this->entityNamespace . '\\' . $metadata->name;

					//var_dump($metadata);

    				$metadatas[] = $metadata;
    				$outputList .= '<li>' . $this->generatedEntityOutputDirectory . '/' . $this->entityNamespace . '/' . $className . '.php </li>'; 
    			}	
    			
    		}

    		//die();
    		
    		
    		
    		$generator = new \Doctrine\ORM\Tools\EntityGenerator();
			$generator->setGenerateAnnotations(true);
			$generator->setBackupExisting(false);
			$generator->setGenerateStubMethods(true);
			$generator->setRegenerateEntityIfExists(true);
			if($this->_getParam('parentClassName') != null){
				if(!file_exists($this->generatedEntityOutputDirectory . '/' . $this->_getParam('parentClassName') . '.php')){
					$parentMetadata = new \Doctrine\ORM\Mapping\ClassMetadataInfo($this->_getParam('parentClassName'));
					$parentMetadata->name = $this->entityNamespace . '\\' . $parentMetadata->name;
					$generator->writeEntityClass($parentMetadata, $this->generatedEntityOutputDirectory);
				}
				$generator->setClassToExtend($this->_getParam('parentClassName'));
			}
			$generator->generate($metadatas,  $this->generatedEntityOutputDirectory);
    		
			echo 'Genereerisin ' . intval(count($metadatas)) . ' Entity klassi'; 
			echo '<ul>' . $outputList . '</ul>';
			
			echo '<br>';
			echo '<a href="/dev/entities">Tagasi entities lehele</a>';
    	}

    }


    
}
	
