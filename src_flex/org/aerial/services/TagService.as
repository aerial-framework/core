package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.TagVO;
	import org.aerial.config;

	public class TagService extends AbstractService
	{
		public function TagService()
		{
			super("TagService", IConfig(new Config()).AMF_GATEWAY, TagVO);
		}
	}
}