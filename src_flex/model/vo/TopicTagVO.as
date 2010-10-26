package model.vo
{
	import org.aerial.rpc.AbstractVO;
	import model.vo.*;
	import mx.collections.ArrayCollection;

	[Bindable]	
	public class TopicTagVO extends AbstractVO
	{		
		public function TopicTagVO()
		{
			super("model.vo.TopicTag", function(field:String):*{return this[field]});
		}
		
		private var _id:*
		private var _topicId:*
		private var _tagId:*

		//Relations
		private var _Topic:*
		private var _Tag:*

			
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

		//Relations
			
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