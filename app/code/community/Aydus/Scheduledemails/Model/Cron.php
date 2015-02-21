<?php

/**
 * Scheduling
 *
 * @category    Aydus
 * @package     Aydus_Scheduledemails
 * @author     	Aydus Consulting <davidt@aydus.com>
 */
class Aydus_Scheduledemails_Model_Cron extends Mage_Core_Model_Abstract 
{
	
	protected $_jobCode;
	protected $_campaignId;
	
	protected $_frequency;
	protected $_monthDay;
	protected $_weekDay;
	protected $_startTime;

	protected $_startHour;
	protected $_startMinute;
	protected $_startSecond;
		
	public function generateSchedules($campaignId, $jobCode, $frequency, $monthDay, $weekDay, $startTime) 
	{
		$this->_campaignId = $campaignId;
		$this->_jobCode = $jobCode;
		$this->_frequency = $frequency;
		$this->_monthDay = $monthDay;
		$this->_weekDay = $weekDay;
		$this->_startTime = $startTime;
		
		$startTime = explode(",",$this->_startTime);
		$this->_startHour = $startTime[0];
		$this->_startMinute = $startTime[1];
		$this->_startSecond = $startTime[2];
				
		$nowDatetime = date("Y-m-d H:i:s");
		$oneHourAgo = date("Y-m-d H:i:s",time()-3600);
		$fiveMinutesAgo = date("Y-m-d H:i:s",time()-300);
		$now = date("Y-m-d H:i:s");
		$fiveMinutesFromNow = date("Y-m-d H:i:s", strtotime($now)+300);
		
		$schedule = Mage::getModel('cron/schedule');

		//clear out scheduled
		$hungCollection = $schedule->getCollection();
		$hungCollection->addFieldToFilter('job_code', $this->_jobCode)
			->addFieldToFilter('status', array('pending', 'running', 'missed', 'success'))
			->addFieldToFilter('scheduled_at', array('lteq'=> $oneHourAgo));
		if ($hungCollection->getSize()>0){
		
			foreach ($hungCollection  as $hung){
				$hung->delete();
			}
		}
		//check if running
		$runningCollection = $schedule->getCollection();
		$runningCollection->addFieldToFilter('job_code', $this->_jobCode)
		->addFieldToFilter('status', array('running'))
		->addFieldToFilter('executed_at', array('gteq'=> $oneHourAgo));
		
		if ($runningCollection->getSize()>0){
				
			$running = $runningCollection->getFirstItem();
		
			$message = "Running since ".$running->getExecutedAt();
			return $message;
		} 
				
		//check if scheduled
		$schedulesCollection = $schedule->getCollection();
		$schedulesCollection->addFieldToFilter('job_code', $this->_jobCode)
		->addFieldToFilter('status', array('pending'))
		->addFieldToFilter('scheduled_at', array('gteq'=> $fiveMinutesFromNow));
		
		if ($schedulesCollection->getSize()>0){
			
			$scheduled = $schedulesCollection->getFirstItem();
		
			$message = "Already scheduled at ".$scheduled->getScheduledAt();
		} else {
			
			$message = $this->_generateSchedule();
			Mage::log($message, null, 'aydus_scheduledemails.log');
		}	
		
		return $message;
	}
	
	/**
	 * Schedule 
	 * 
	 * @param string $H Hour
	 * @param string $i minute
	 * @param string $s second
	 * @param string $m month
	 * @param string $d day
	 * @param string $Y Year
	 * @return string
	 */
	public function schedule($H, $i, $s, $m, $d, $Y)
	{
		$createdAt   = strftime("%Y-%m-%d %H:%M:%S",  mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
		$scheduledAt = strftime("%Y-%m-%d %H:%M:%S", mktime($H, $i, $s, $m, $d, $Y));
		
		try {
		
			$cronSchedule = Mage::getModel('cron/schedule');
			$cronSchedule->setJobCode($this->_jobCode)
			->setCreatedAt($createdAt)
			->setScheduledAt($scheduledAt)
			->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)
			->save();
			
			$schedule = Mage::getModel('aydus_scheduledemails/campaign_schedule');
			$schedule->setCampaignId($this->_campaignId)
			->setScheduleId($cronSchedule->getId())
			->save();
										
			return $scheduledAt;
		
		} catch (Exception $e) {
		
			Mage::log($e->getMessage(), null, 'aydus_scheduledemails.log');
			return $e->getMessage();
		}		
	}
	
	protected function _generateSchedule()
	{
		switch ($this->_frequency){
			case "M" :
				$Y = (date("j") > $this->_monthDay) ? date('Y', strtotime('+1 month')) : date("Y");
				$m = (date("j") > $this->_monthDay) ? date('m', strtotime('+1 month')) : date("m");
				$d = $this->_monthDay;
				break;			
			case "W" :
				$weekDayOptions = Mage::app()->getLocale()->getOptionWeekdays();
				foreach ($weekDayOptions as $option){
					$weekDays[] = $option['label'];
				}
				//$weekDays = array(0, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
				$dayOfWeek = $weekDays[$this->_weekDay];
				$time = strtotime("next ". $dayOfWeek);
				$Y = date("Y", $time);
				$m = date("m", $time);
				$d = date("d", $time);
				break;			
			case "D" :
				$Y = date("Y", strtotime("tomorrow"));
				$m = date("m", strtotime("tomorrow"));
				$d = date("d", strtotime("tomorrow"));
				break;			
		}
		
		return $this->schedule($this->_startHour, $this->_startMinute, $this->_startSecond, $m, $d, $Y);
	}
	
}