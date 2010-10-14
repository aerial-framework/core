package org.aerial.rpc
{
	import flash.utils.describeType;
	import flash.utils.getDefinitionByName;
	import flash.net.registerClassAlias;
	
	import mx.binding.utils.ChangeWatcher;
	import mx.events.PropertyChangeEvent;
	
	
	public class AbstractVO implements IAbstractVO
	{
		private var vo:Object;
		
		public function AbstractVO(aliasName:String)
		{
			vo = new Object();
			vo["_explicitType"] = aliasName;
			
			var descr:XML = describeType(this);
			var props:XMLList = descr..accessor + descr..variable;

			for each(var prop:XML in props)
			{ 
				ChangeWatcher.watch(this, prop.@name.toString(), propertyChanged);
				vo[prop.@name.toString()] = undefined;				
			}
			
			var voType:String = descr.@name.toString();
			voType = voType.replace("::", ".");
			var voClass:Class = getDefinitionByName(voType) as Class;
			
			registerClassAlias(aliasName, voClass); 
		}
		
		private function propertyChanged(event:PropertyChangeEvent):void
		{
			if(event.newValue)
				vo[event.property.toString()] = event.newValue;
		}
		
		public function getObject():Object
		{
			return vo;
		}
		
		
		
	}
}