package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.PostTagVO;
	import org.aerial.config.Config;

	public class PostTagService extends AbstractService
	{
		public function PostTagService()
		{
			super("PostTagService", Config.SERVER_URL, PostTagVO);
		}
	}
}