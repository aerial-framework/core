package model.services.base
{
	import flash.utils.describeType;

	import model.vo.TagVO;
	import AMFPHP_GATEWAY_URL;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BaseTagService extends RemoteObject
	{
	
		public const FIND:String = "TagService.find";
		public const FIND_BY_FIELD:String = "TagService.findByField";
		public const FIND_BY_FIELDS:String = "TagService.findByFields";
		public const FIND_WITH_RELATED:String = "TagService.findWithRelated";
		public const FIND_ALL_WITH_RELATED:String = "TagService.findAllWithRelated";
		public const FIND_RELATED:String = "TagService.findRelated";
		public const FIND_ALL:String = "TagService.findAll";
		public const SAVE:String = "TagService.save";
		public const UPDATE:String = "TagService.update";
		public const DROP:String = "TagService.drop";
		public const COUNT:String = "TagService.count";
		public const COUNT_RELATED:String = "TagService.countRelated";
		

		public function BaseTagService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = ;
			this.source = "TagService";
		}
		
		public function find(tag_id:uint):void
		{
			this.getOperation("find").send(tag_id);
		}
		
		public function findByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByField").send(field, value, paged, limit, offset);
		}
		
		public function findByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByFields").send(fields, values, paged, limit, offset);
		}
		
		public function findWithRelated(tag_id:uint):void
		{
			this.getOperation("findWithRelated").send(tag_id);
		}
		
		public function findAllWithRelated(criteria:Object=null):void
		{
			this.getOperation("findAllWithRelated").send(criteria);
		}
		
		public function findRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: topicTags, Type: many
			//		Alias: postTags, Type: many
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function findAll(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findAll").send(paged, limit, offset);
		}
								   
		public function save(tag:TagVO):void
		{
			this.getOperation("save").send(tag);
		}
		
		public function update(tag:TagVO):void
		{
			
			var reflection:XML = describeType(tag);
			var props:XMLList = reflection..variable + reflection..accessor;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = tag[property.@name];
			
			this.getOperation("update").send(tag.id, fields);
		}
		
		public function drop(tag:TagVO):void
		{
			this.getOperation("drop").send(tag);
		}
		
		public function count():void
		{
			this.getOperation("count").send();
		}
		
		public function countRelated(field:String, tag_id:uint):void
		{
			this.getOperation("countRelated").send(field, tag_id);
		}
	}
}