package model.services.base
{
	import flash.utils.describeType;

	import model.vo.TopicVO;
	import com.forum.config.Config;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BaseTopicService extends RemoteObject
	{
	
		public const GET_TOPIC:String = "TopicService.getTopic";
		public const GET_TOPIC_BY_FIELD:String = "TopicService.getTopicByField";
		public const GET_TOPIC_BY_FIELDS:String = "TopicService.getTopicByFields";
		public const GET_TOPIC_WITH_RELATED:String = "TopicService.getTopicWithRelated";
		public const GET_ALL_TOPIC_WITH_RELATED:String = "TopicService.getAllTopicWithRelated";
		public const GET_RELATED:String = "TopicService.getRelated";
		public const GET_ALL_TOPICS:String = "TopicService.getAllTopics";
		public const SAVE_TOPIC:String = "TopicService.saveTopic";
		public const UPDATE_TOPIC:String = "TopicService.updateTopic";
		public const DELETE_TOPIC:String = "TopicService.deleteTopic";
		public const COUNT_TOPICS:String = "TopicService.countTopics";
		public const COUNT_RELATED:String = "TopicService.countRelated";
		

		public function BaseTopicService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = Config.GATEWAY_URL;
			this.source = "TopicService";
		}
		
		public function getTopic(topic_id:uint):void
		{
			this.getOperation("getTopic").send(topic_id);
		}
		
		public function getTopicByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getTopicByField").send(field, value, paged, limit, offset);
		}
		
		public function getTopicByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getTopicByFields").send(fields, values, paged, limit, offset);
		}
		
		public function getTopicWithRelated(topic_id:uint):void
		{
			this.getOperation("getTopicWithRelated").send(topic_id);
		}
		
		public function getAllTopicWithRelated(criteria:Object=null):void
		{
			this.getOperation("getAllTopicWithRelated").send(criteria);
		}
		
		public function getRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: Category, Type: one
			//		Alias: User, Type: one
			//		Alias: posts, Type: many
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function getAllTopics(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("getAllTopics").send(paged, limit, offset);
		}
								   
		public function saveTopic(topic:TopicVO):void
		{
			this.getOperation("saveTopic").send(topic, topic.getRelatedData());
		}
		
		public function updateTopic(topic:TopicVO):void
		{
			
			var reflection:XML = describeType(topic);
			var props:XMLList = reflection..variable + reflection..accessor;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = topic[property.@name];
			
			this.getOperation("updateTopic").send(topic.id, fields);
		}
		
		public function deleteTopic(topic:TopicVO):void
		{
			this.getOperation("deleteTopic").send(topic);
		}
		
		public function countTopics():void
		{
			this.getOperation("countTopics").send();
		}
		
		public function countRelated(field:String, topic_id:uint):void
		{
			this.getOperation("countRelated").send(field, topic_id);
		}
	}
}