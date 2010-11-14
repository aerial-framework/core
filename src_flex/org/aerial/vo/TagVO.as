package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	
	[Bindable]	
	public class TagVO extends AbstractVO
	{		
		public function TagVO()
		{
			super("org.aerial.vo.Tag", function(field:String):*{return this[field]});
		}
		
		private var id:*
		private var name:*
		private var topicTags:*
		private var postTags:*

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

		public function get topicTags():TopicTagVO
		{
			return _topicTags;
		}
		
		public function set topicTags(value:TopicTagVO):void
		{
			_topicTags = value;
		}

		public function get postTags():PostTagVO
		{
			return _postTags;
		}
		
		public function set postTags(value:PostTagVO):void
		{
			_postTags = value;
		}		
	}
}