<?php
    class PrestationController extends Controller {

        var $rolepermissions = [1,2,3];

        public function index() {
            $user = new InternalUser($_SESSION["user"]["idinternaluser"]);
            if (is_student()) {
                $student = Student::findOne(["idinternaluser" => $user->idinternaluser])[0];
                $this->student($student);
            } else if (is_teacher() || is_admin()) {
                $this->corrector($user);
            } else {
                go_back();
            }
        }

        // STUDENT 

        public function student($student) {
            $prestations = Prestation::findOne(["idstudent" => $student->idinternaluser->idinternaluser]);

            $prestations = array_filter($prestations, function($prestation) {
                return strtotime($prestation->date_prestation) > strtotime(date("Y-m-d H:i:s"));
            });

            $table_header = array("Evenement", "Eleve", "Salle", "Jury", "Date");

            $table_content = array();
            foreach ($prestations as &$prestation) {
                $table_content[$prestation->idprestation] = array(
                    "Evenement" => $prestation->idevent->entitled_event,
                    "Eleve" => $prestation->idstudent->idinternaluser->nom." ".$prestation->idstudent->idinternaluser->prenom,
                    "Salle" => $prestation->idjury->idclassroom->name_classroom,
                    "Jury" => $prestation->idjury->name_jury,
                    "Date" => date_format(date_create($prestation->date_prestation),'d/m/y'). " de " . $prestation->start_time . " à " . $prestation->end_time
                );
            }

            $this->renderComponent("table", ["header" => $table_header, "content" => $table_content]);
        }

        public function corrector($user) {
            if(is_teacher()){
                $prestations = $user->getEnseignantPrestations();
            }else if(is_admin()){
                // TODO : select if admin
                $prestations = $user->getEnseignantPrestations();
            }

            foreach ($prestations as $key => $prestation) {
                $prestations[$key] = new Prestation($prestation["idprestation"]);
            }

            $prestations = array_filter($prestations, function($prestation) {
                return strtotime($prestation->date_prestation) > strtotime(date("Y-m-d H:i:s"));
            });

            $table_header = array("Evenement", "Eleve", "Salle", "Jury", "Date");

            $table_content = array();
            foreach ($prestations as &$prestation) {
                $table_content[$prestation->0] = array(
                    "Evenement" => $prestation->idevent->entitled_event,
                    "Eleve" => $prestation->idstudent->idinternaluser->nom." ".$prestation->idstudent->idinternaluser->prenom,
                    "Salle" => $prestation->idjury->idclassroom->name_classroom,
                    "Jury" => $prestation->idjury->name_jury,
                    "Date" => date_format(date_create($prestation->date_prestation),'d/m/y'). " de " . $prestation->start_time . " à " . $prestation->end_time
                );
            }

            $table_actions = array(
                array("url" => "?r=prestation/notation&id=:id", "desc" => "", "icon" => "updatepasswordicon.png")
            );
        
            $this->renderComponent("table", ["header" => $table_header, "content" => $table_content, "actions" => $table_actions]);    
        }

        public function notation(){
            id_or_back(parameters());

            $prestation = new Prestation(parameters()['id']);
            $id_event = $prestation->idevent->idevent;
            $evaluations_criterias = EvaluationCriteria::findOne(['idevent' => $id_event]);

            if($_SERVER['REQUEST_METHOD'] == "GET"){ 

                $form_title = "Noter une prestation";

                $form_content = array();

                foreach($evaluations_criterias as $criteria){

                    $individual_evaluation = IndividualEvaluation::findOne(['idevaluationcriteria' => $criteria->idevaluationcriteria]);
                    
                    if($individual_evaluation && $individual_evaluation[0]->idcompose->idinternaluser->idinternaluser == get_id()){
                        $form_content[$criteria->description_criteria] = array("type" => "text", "placeholder" => $individual_evaluation[0]->individual_note, "value" => $individual_evaluation[0]->individual_note);
                    }else{
                        $form_content[$criteria->description_criteria] = array("type" => "text", "placeholder" => $criteria->scale_criteria);
                    }
                }

                $this->renderComponent("form", ["title" => $form_title, "content" => $form_content]);
            }else{
                
                print_r(parameters());
            }
        }

    }



