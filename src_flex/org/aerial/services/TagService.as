package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.TagVO;
	import org.aerial.config.Config;

	public class TagService extends AbstractService
	{
		public function TagService()
		{
			super("TagService", Config.SERVER_URL, TagVO);
		}
	}
}