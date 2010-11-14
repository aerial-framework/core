package model.services.base
{
	import flash.utils.describeType;

	import model.vo.PostTagVO;
	import AMFPHP_GATEWAY_URL;
	
	import mx.messaging.config.ServerConfig;
	import mx.rpc.remoting.RemoteObject;
	
	public class BasePostTagService extends RemoteObject
	{
	
		public const FIND:String = "PostTagService.find";
		public const FIND_BY_FIELD:String = "PostTagService.findByField";
		public const FIND_BY_FIELDS:String = "PostTagService.findByFields";
		public const FIND_WITH_RELATED:String = "PostTagService.findWithRelated";
		public const FIND_ALL_WITH_RELATED:String = "PostTagService.findAllWithRelated";
		public const FIND_RELATED:String = "PostTagService.findRelated";
		public const FIND_ALL:String = "PostTagService.findAll";
		public const SAVE:String = "PostTagService.save";
		public const UPDATE:String = "PostTagService.update";
		public const DROP:String = "PostTagService.drop";
		public const COUNT:String = "PostTagService.count";
		public const COUNT_RELATED:String = "PostTagService.countRelated";
		

		public function BasePostTagService(destination:String="aerial")
		{
			super(destination);
			
			this.endpoint = ;
			this.source = "PostTagService";
		}
		
		public function find(posttag_id:uint):void
		{
			this.getOperation("find").send(posttag_id);
		}
		
		public function findByField(field:String, value:*, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByField").send(field, value, paged, limit, offset);
		}
		
		public function findByFields(fields:Array, values:Array, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findByFields").send(fields, values, paged, limit, offset);
		}
		
		public function findWithRelated(posttag_id:uint):void
		{
			this.getOperation("findWithRelated").send(posttag_id);
		}
		
		public function findAllWithRelated(criteria:Object=null):void
		{
			this.getOperation("findAllWithRelated").send(criteria);
		}
		
		public function findRelated(field:String, id:uint, paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			//	available relations:
			//		Alias: Post, Type: one
			//		Alias: Tag, Type: one
			
			this.getOperation("getRelated").send(field, id, paged, limit, offset);
		}
		
		public function findAll(paged:Boolean=false, limit:int=0, offset:int=0):void
		{
			this.getOperation("findAll").send(paged, limit, offset);
		}
								   
		public function save(posttag:PostTagVO):void
		{
			this.getOperation("save").send(posttag);
		}
		
		public function update(posttag:PostTagVO):void
		{
			
			var reflection:XML = describeType(posttag);
			var props:XMLList = reflection..variable + reflection..accessor;
			
			var fields:Object = {};
			for each(var property:XML in props)
				fields[property.@name] = posttag[property.@name];
			
			this.getOperation("update").send(posttag.id, fields);
		}
		
		public function drop(posttag:PostTagVO):void
		{
			this.getOperation("drop").send(posttag);
		}
		
		public function count():void
		{
			this.getOperation("count").send();
		}
		
		public function countRelated(field:String, posttag_id:uint):void
		{
			this.getOperation("countRelated").send(field, posttag_id);
		}
	}
}