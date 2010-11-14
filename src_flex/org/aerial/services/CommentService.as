package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	import org.aerial.system.IConfig;
	
	import org.aerial.vo.CommentVO;
	import org.aerial.config;

	public class CommentService extends AbstractService
	{
		public function CommentService()
		{
			super("CommentService", IConfig(new Config()).AMF_GATEWAY, CommentVO);
		}
	}
}