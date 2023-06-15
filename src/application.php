<?php
/**
 * Represents an application on the server
 */
class App {

    private $name;
    private $version;
    private $executablePath;
    private $state;
    private static $database = "my_database";
    
    /**
     * Creates a new instance of App
     *
     * @param string $name Name of the application.
     * @param string $version Version of the application.
     * @param string $executablePath Path of the application executable.
     */
    public function __construct(string $name, string $version, string $executablePath) {
        $this->name = $name;
        $this->version = $version;
        $this->executablePath = $executablePath;
    }
    /**
     * gets the name of the app
     * @return The name of the app
     */
    public function getName(): string {
        return $this->name;
    }
    /**
     * sets the name of the app
     * @param string $name New name of the application.
     */
    public function setName(string $name): void {
        $this->name = $name;
    }
    /**
     * gets the version of the app.
     * @return The version of the app.
     */
    public function getVersion(): string {
        return $this->version;
    }
    /**
     * sets the version of the app
     * @param string $version New version of the application.
     */
    public function setVersion(string $version): void {
        $this->version = $version;
    }
    /**
     * gets the path of the app.
     * @return The path of the app.
     */
    public function getExecutablePath(): string {
        return $this->executablePath;
    }

    /**
     * sets the path of the app
     * @param string $executablePath New path of the application.
     */
    public function setExecutablePath(string $executablePath): void {
        $this->executablePath = $executablePath;
    }

    private function isRunning() {
        // Check if the app is running by looking for its process ID.
        $pid = exec("pgrep -f " . pathinfo($this->executablePath)['basename']);

        // If the app is running, return its process ID.
        // Otherwise, return false.
        return $pid !== false;
    }

    /**
     * starts the application
     */
    public function start(): void {
	echo "Starting app $this->name (v$this->version@$this->executablePath) ...";
	$this->state = true;
	exec("nohup " . $this->executablePath . "&");
        $this->syncToDatabase();
    }
    /**
     * stops the application
     */
    public function stop(): void {
	echo "Stopping app $this->name(v$this->version)...";
	$this->state = false;
	if($this->isRunning()) {
		exec("pkill -f " . pathinfo($this->executablePath)['basename']);
	}
	$this->syncToDatabase();
    }

    private function syncToDatabase(): void {
        $db = new PDO(self::$database);
        $sql = "INSERT INTO `app_data` (`name`, `version`, `path`, `running`) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$this->name, $this->version, $this->path, $this->state]);
    }

}

?>

