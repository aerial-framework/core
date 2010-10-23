package model.vo
{
	import org.aerial.rpc.AbstractVO;
	import model.vo.*;
	import mx.collections.ArrayCollection;

	[Bindable]	
	public class TagVO extends AbstractVO
	{		
		public function TagVO()
		{
			super("model.vo.Tag", function(field:String):*{return this[field]});
		}
		
		private var _id:*
		private var _name:*

		//Relations
		private var _topicTags:*
		private var _postTags:*

			
		public function get id():int
		{
			return _id;
		}
		
		public function set id(value:int):void
		{
			_id = value;
		}
			
		public function get name():String
		{
			return _name;
		}
		
		public function set name(value:String):void
		{
			_name = value;
		}

		//Relations
			
		public function get topicTags():ArrayCollection
		{
			return _topicTags;
		}
		
		public function set topicTags(value:ArrayCollection):void
		{
			_topicTags = value;
		}
			
		public function get postTags():ArrayCollection
		{
			return _postTags;
		}
		
		public function set postTags(value:ArrayCollection):void
		{
			_postTags = value;
		}
		
	}
}