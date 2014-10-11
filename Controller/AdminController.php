<?php
    
    namespace Controller;

    use \View\AdminView;
    use \View\AdminAjaxView;
    use \Model\SkillManager;
    use \Model\TranslationManager;
    use \Model\StatManager;
    use \Utils\SecurityHelper as SH;

    class AdminController extends Controller {

        /**
         * Home page
         */
        public function statsAction(){

            SH::lock("superadmin");
            $params = array();

            $statManager = new StatManager();
            
            $params['title'] = "Statshit";

            $params['skillsCount'] = $statManager->countLabel("Skill");
            $params['usersCount'] = $statManager->countLabel("User");
            $params['maxDepth'] = $statManager->getMaxDepth();
            $params['latestChanges']= $statManager->getLatestChanges();
            //$params['meanNumber'] = $statManager->getMeanNumberOfSkillChildren();

            $view = new AdminView("stats.php", $params);
            
            $view->send();
        }

        public function latestChangesAction(){
            SH::lock("superadmin");
            $params = array();

            $statManager = new StatManager();
            $params['latestChanges']= $statManager->getLatestChanges();

            $view = new AdminAjaxView("latest_changes.php", $params);
            
            $view->send();
        }


        public function powerEditAction(){
            SH::lock("superadmin");
            
            $skillManager = new SkillManager();
            $user = SH::getUser();

            if (!empty($_POST)){
                $skillUuid = $_POST['skillUuid'];
                $nameFr = $_POST['nameFr'];
                $nameEn = $_POST['nameEn'];

                $skill = $skillManager->findByUuid($skillUuid);

                if ($skill){
                    $previousName = $skill->getName();
                    //only save if different
                    //english different ?
                    if ($nameEn != $skill->getName()){
                        $skill->setName(SH::safe($nameEn));
                        $skillManager->update($skill, $user->getUuid(), $previousName);
                    }
                    //french different ?
                    if ($nameFr != $skill->getName()){
                        $translationManager = new TranslationManager();

                        //insert or update, the same
                        $translationManager->saveSkillTranslation("fr", $nameFr, $skill, false);

                    }
                }

            }
            die("ok");
        }

    }