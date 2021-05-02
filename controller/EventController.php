<?php

class EventController extends Controller {

	var $rolepermissions = [1,2,3];

	public function index() {
		$events = Event::findAll();
		$events = array_filter($events, function($event) {
			return strtotime($event->end_date) > strtotime(date("Y-m-d H:i:s"));
		});
		$this->render("index", $events);
	}

	public function historique() {
		$events = Event::findAll();
		$events = array_filter($events, function($event) {
			return strtotime($event->end_date) < strtotime(date("Y-m-d H:i:s"));
		});
		$this->render("index", $events);
	}

	public function view() {
		try {
			$object = new Event(parameters()["id"]);
			$this->render("view", array('object' => $object));
		} catch (Exception $e) {
			(new SiteController())->render("index");
			// $this->render("error");
		}
	}

	public function update_view(){
		$object = new Event(parameters()["id"]);

		foreach(Role::findAll() as $role){
			if($role->name_role == "Etudiant"){
				$role_ban = $role->idrole;
			}
		}

		foreach(InternalUser::findAll() as $user){
			if($user->idinternaluser == $object->idevent_creator){
				$actual_creator = $user;
			}
		}

		
		$this->render("update", array('object' => $object,'internaluser' => InternalUser::findAll(), 'role_ban' => $role_ban, 'actual_creator' => $actual_creator));
	}

	public function update_event(){
		if(isset(parameters()['idevent_creator']) and parameters()['idevent_creator']  != 0 and isset(parameters()['entitled_event']) and isset(parameters()['description_event']) and isset(parameters()['start_date']) and isset(parameters()['end_date']) and isset(parameters()['idevent'])){

			$event = new Event(parameters()['idevent']);
			$event->entitled_event = parameters()["entitled_event"];
			$event->description_event = parameters()["description_event"];
			$event->start_date = parameters()["start_date"];
			$event->end_date = parameters()["end_date"];
			$event->idevent_creator = parameters()['idevent_creator'];
			
			$event->update();
		}
		$this->render("index", array('event' => Event::findAll(),'classroom' => Classroom::findAll(), 'internaluser' => InternalUser::findAll()));

	}

	public function add_event(){
		if(isset(parameters()['idevent_creator']) and parameters()['idevent_creator']  != 0 and isset(parameters()['entitled_event']) and isset(parameters()['description_event']) and isset(parameters()['start_date']) and isset(parameters()['end_date'])){

			$event = new Event();
			$event->entitled_event = parameters()["entitled_event"];
			$event->description_event = parameters()["description_event"];
			$event->start_date = parameters()["start_date"];
			$event->end_date = parameters()["end_date"];
			$event->idevent_creator = parameters()['idevent_creator'];
			
			$event->insert();
		}
		$this->render("index", array('event' => Event::findAll(),'classroom' => Classroom::findAll(), 'internaluser' => InternalUser::findAll()));

	}

	public function add() {

		if($_SERVER['REQUEST_METHOD'] == "GET") {
			$form_title = "Ajouter un évènement";

			$teachers = InternalUser::findOne(["idrole"=>2]);
			$options = array();
			foreach ($teachers as &$teacher) {
				$options[$teacher->nom." ".$teacher->prenom] = $teacher->idinternaluser;
				
			}


			$form_content = array(
				"Titre"=>array("type"=>"text"),
				"Description"=>array("type"=>"text"),
				"Choix de l'enseignant responsable de l'évenement :"=>array(
					"type"=>"select",
					"desc"=>"Choisissez une option",
					"options"=>$options),
				"Date"=>array(
					"type"=>"date",
					"title"=>"Date de début et de fin")
						
			);
			$this->renderComponent("form", ["title"=>$form_title, "content"=>$form_content]);
		} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
			var_dump(parameters());
		}

		
	}

}