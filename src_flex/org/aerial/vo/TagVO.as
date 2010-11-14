package org.aerial.vo
{
	import org.aerial.rpc.AbstractVO;
	import org.aerial.vo.*;
	import mx.collections.ArrayCollection;
	import flash.net.registerClassAlias;
	
	[Bindable]
	[RemoteClass(alias="org.aerial.vo.Tag")]	
	public class TagVO extends AbstractVO
	{
		public function TagVO()
		{
			super("org.aerial.vo.Tag",
							function(field:String):*{return this[field]},
							function(field:String, value:*):void{this[field] = value});
		}
		
		private var _id:*
		private var _name:*
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