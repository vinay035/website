<?php
    
    namespace Controller;

    use \View\AdminView;
    use \View\AdminAjaxView;
    use \Model\SkillManager;
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


    }