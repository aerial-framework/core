package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	
	[Bindable]	
	public class TopicTagVO extends AbstractVO
	{		
		public function TopicTagVO()
		{
			super("org.aerial.vo.TopicTag", function(field:String):*{return this[field]});
		}
		
		private var id:*
		private var topicId:*
		private var tagId:*
		private var Topic:*
		private var Tag:*

		public function get id():int
		{
			return _id;
		}
		
		public function set id(value:int):void
		{
			_id = value;
		}

		public function get topicId():int
		{
			return _topicId;
		}
		
		public function set topicId(value:int):void
		{
			_topicId = value;
		}

		public function get tagId():int
		{
			return _tagId;
		}
		
		public function set tagId(value:int):void
		{
			_tagId = value;
		}

		public function get Topic():TopicVO
		{
			return _Topic;
		}
		
		public function set Topic(value:TopicVO):void
		{
			_Topic = value;
		}

		public function get Tag():TagVO
		{
			return _Tag;
		}
		
		public function set Tag(value:TagVO):void
		{
			_Tag = value;
		}		
	}
}