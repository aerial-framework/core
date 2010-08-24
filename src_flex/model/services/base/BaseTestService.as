package model.services.base
{
	import flash.utils.describeType;
	
	import model.vo.TopicVO;
	import com.forum.config.Config;
	
	import mx.controls.Tree;
	import mx.rpc.AsyncToken;
	import mx.rpc.CallResponder;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.remoting.RemoteObject;
	
	public class BaseTestService extends RemoteObject
	{
		
		public function BaseTestService(destination:String="aerial")
		{
			super(destination);
			this.endpoint = Config.GATEWAY_URL;
			this.source = "TopicService";
		}
		
		public var callBack:Function = null; 
		
		public var callResponder:CallResponder;
	
		
		private var _relations:Array = null;
		
		public function get relations():Array
		{
			return _relations;
		}

		public function set relations(value:Array):void
		{
			_relations = value;
		}

		
		public function depth(parents:int=0, childre:int=0, sibbling:int=0):void{
			
		}
		
		// ================ Find ===================

		public function findAll(page:int=0):void
		{
			if(callBack==null) this.getOperation("findAll").addEventListener(ResultEvent.RESULT, callBack);
			var token:AsyncToken = this.getOperation("findAll").send(page, this.relations);
			
		}
		
		
		public function findFirst():void
		{
			this.getOperation("findFirst").send(this.relations);	
		}
		
		
		public function findLast():void
		{
			this.getOperation("findLast").send(this.relations);	
		}
		
		public function findById(id:int, page:int=0):void
		{
			this.getOperation("findById").send(id, page, this.relations);
		}
		
		
		public function findByField(field:Object, page:int):void
		{
			this.getOperation("findByField").send(field, page, this.relations);
		}
		
		
		public function findByParent(parent:Object, page:int=0):void
		{
			this.getOperation("findByParent").send(parent, page, this.relations);
		}
		
		
		public function findByExample(example:Object, page:int=0):void
		{
			this.getOperation("findByExample").send(example, page, this.relations);			
		}
		
		// ======================  Modify =====================
		
		public function insert(topic:TopicVO):void
		{
			this.getOperation("insert").send(topic);
		}
		
		
		public function update(topic:TopicVO):void
		{
			this.getOperation("update").send(topic);
		}
		
		
		public function drop(topic:TopicVO):void
		{
			this.getOperation("delete").send(topic);
		}
		
		
		public function save(topic:TopicVO):void
		{
			this.getOperation("save").send(topic);
		}
		
		// ======================= Helpers ======================
		
		public function count(example:Object=null):void
		{
			this.getOperation("count").send(example);
		}
		
		
		
	}
}

