<?php
namespace Repository;

use Repository\AbstractRepository;
use Doctrine\ORM\Query\ResultSetMapping;

		function sec2hms ($sec)
		{
		    $hms = "";
		    $hours = intval(intval($sec) / 3600);
		    $hms .= str_pad($hours, 2, "0", STR_PAD_LEFT). ':';
		    $minutes = intval(($sec / 60) % 60);
		    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
		    $seconds = intval($sec % 60);
		    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
		    return $hms;
		}


class SetSets extends AbstractRepository {

	// 	$this->em->getRepository('\Entity\SetSets')->searchTrainingPlans($sets, $coach, $sportId, $intensity, $event, $name, $limit, $offset);
	public function searchTrainingPlans($nr, $sets, $coach, $sportId , $intensity, $event, $name, $limit = null, $offset = null) {
		
		$where_set = 0;

		$query = $this->createQueryBuilder('SetSets');
		$query->leftJoin('SetSets.user', 'User');
		$query->orderBy('SetSets.id', 'DESC');
	

		if ($sets != null) {
			$query->andWhere('SetSets.id = :sets')->setParameter('sets', $sets);
			$where_set = 1;
		}

		if ($name != null) {
			$query->andWhere('SetSets.name LIKE :name')->setParameter('name', '%' . $name . '%');
			$where_set = 1;
		}

		if ($sportId != null) {
			$query->andWhere('SetSets.sport = :sportId')->setParameter('sportId', $sportId);
			$where_set = 1;
		}

		if ($intensity != null) {
			$query->andWhere('SetSets.intensity = :intensity')->setParameter('intensity', $intensity);
			$where_set = 1;
		}

		if ($event != null) {
			$query->andWhere('SetSets.event = :event')->setParameter('event', $event);
			$where_set = 1;
		}
	
		if ($coach != null) {
			$query->andWhere('User.name = :coach')->setParameter('coach', $coach);
			$where_set = 1;
		}

		if ($limit != null) {
			$query->setMaxResults($limit);
		}
	
		if ($offset != null) {
			$query->setFirstResult($offset);
		}

		if ($where_set==0) return array();
	
		$result = $query->getQuery()->getResult();
		$return_result = array();
		foreach ($result as $row) {
			$row->SetDetails($this->trainingPlanSetDetails($row,$nr));
			$return_result[]=$row;
		}

		return $return_result;
	}


	// @param - '\Entity\SetSets', workout nr
	private function trainingPlanSetDetails($setSets, $nr) {

		$intensity=array('','Low intensity','Medium intensity','High intensity','');
        $days_between=array('Today','Tomorrow');

		$return_array = array();

		$return_array['nr'] = $nr;

		$return_array['intensity'] = $intensity[$setSets->getIntensity()];

		$return_array['execution_order'] = $nr;

		$rsm = new ResultSetMapping;
		$rsm->addScalarResult('id', 'id');
		$rsm->addScalarResult('name', 'name');
		$rsm->addScalarResult('date', 'date');
		$rsm->addScalarResult('date_orginal', 'date_orginal');
		$rsm->addScalarResult('days_between', 'days_between');
		$query = $this->_em->createNativeQuery(
			"select 
				SetTrainingPlan.id, 
				SetTrainingPlan.name, 
				SetTrainingPlan.date date_orginal, 
				date_format(SetTrainingPlan.date,'%W<br>%d %M') date, 
				DATEDIFF( SetTrainingPlan.date, CURDATE() ) days_between 
			from SetTrainingPlan 
			where 
				set_id=? and execution_order=?"	, $rsm);
		$query->setParameter(1, $setSets->getID());
		$query->setParameter(2, $nr);
		$result = $query->getResult();

		$return_array = array_merge($return_array,$result[0]);

        if (isset($days_between[$return_array['days_between']])) $return_array['days_between_html']=$days_between[$return_array['days_between']];
        	else $return_array['days_between_html']=$return_array['date'];

		$rsm = new ResultSetMapping;
		$rsm->addScalarResult('max_nr', 'max_nr');
		$query = $this->_em->createNativeQuery("select max(execution_order) max_nr from SetTrainingPlan where set_id=?", $rsm);
		$query->setParameter(1, $setSets->getID());
		$result = $query->getResult();

		$return_array['max_nr'] = $result[0]['max_nr'];

		$rsm = new ResultSetMapping;
		$rsm->addScalarResult('name', 'name');
		$rsm->addScalarResult('intensity', 'intensity');
		$rsm->addScalarResult('note', 'note');
		$rsm->addScalarResult('distance', 'distance');
		$rsm->addScalarResult('duration', 'duration');
		$query = $this->_em->createNativeQuery('select * from SetExercise where trainingPlanId=?', $rsm);
		$query->setParameter(1, $return_array['id']);
		$result = $query->getResult();

		$return_array['steps'] = $result;


        $goal_column='distance'; if ($return_array['steps'][0]['duration'] <> 0) $goal_column='duration';              
        //visu sareekinam un samapojam
        $kopa = 0; foreach ($return_array['steps'] as $row) $kopa=$kopa+$row[$goal_column];
        if ($goal_column=='distance') $kopa_html = ($kopa/1000)." km"; else $kopa_html = sec2hms($kopa);

        $return_array['goal_column'] = $goal_column;
        $return_array['kopa'] = $kopa;
        $return_array['kopa_html'] = $kopa_html;



        $workout_text="";
        $i=0; foreach ($return_array['steps'] as $row) {
            $class="class=\"sub\"";
            if ($i==0 or $i==count($return_array['steps'])-1) {$class="";}
            if ($return_array['goal_column']=='distance') {
                $goal_html = ($row['distance']/1000)." km";
            } else {
                $goal_html = sec2hms($row['duration']);
            }
            $workout_text.="<p ".$class.">".$goal_html." ".$row['name']." <b>".$intensity[$row['intensity']]."</b> <span class=\"infoText\"> ".$row['note']."</span></p>";
            $i++; 
        }

        $workout_graph="";
        $i=0; $proc_sum=0; foreach ($return_array['steps'] as $row) {
            if ($row['intensity']==4) $row['intensity']=0;
          
            $proc = round(($row[$return_array['goal_column']]*100/$return_array['kopa']),2);
            $proc_sum = $proc_sum + $proc;
            if ($i==count($return_array['steps'])-1) $proc = $proc + (100-$proc_sum);

            $workout_graph.="<div proc=\"".$proc_sum."\" style=\"width: ".$proc."%;\" class=\"workout0".($row['intensity']+1);
            if ($i==0) $workout_graph.=" first";
            if ($i==count($return_array['steps'])-1) $workout_graph.=" last";
            $workout_graph.="\">";
            if ($i<>count($return_array['steps'])-1) { $workout_graph.="<span></span>"; }
            $workout_graph.="</div>";
            $i++; 
        }


        $return_array['workout_graph'] = $workout_graph;
        $return_array['workout_text'] = $workout_text;

		error_log(print_r($return_array,true));

		return $return_array;
	}

}